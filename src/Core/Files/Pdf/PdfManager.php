<?php

namespace App\Core\Files\Pdf;

use App\Core\Files\FileNameManager;
use App\Core\Files\Pdf\Compiler\Impl\PdflatexLatexCompiler;
use App\Core\Files\Pdf\Compiler\LatexCompilationFailedException;
use App\Core\Files\Pdf\Compiler\LatexCompiler;
use App\Core\Files\Pdf\Compiler\LatexCompilerNotFoundException;
use App\Core\Files\Pdf\Merger\PdfMerger;
use App\Core\Files\Pdf\Merger\PdfMergerNotFoundException;
use App\Core\Renderer\RendererRepository;
use App\Entity\Correcteur;
use App\Entity\EchelleGraphique;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Environment;
use ZipArchive;

/**
 * Fonctions permettant la production de fichiers liés à la compilation Latex
 * Elle gère en entrée des entités symfony, et rend en sortie des réponses http pointant vers des fichiers existants
 * Les trois principales méthodes correspondent à la production de fichiers pdf simples, de fichiers pdf fusionnés et de fichiers zip contenant de multiples fichiers pdf simples.
 */
class PdfManager
{

    const MERGED_PDF_FILE_NAME = "merged.pdf";
    const TEMP_ZIP_FILE_NAME = "temp.zip";

    const TEX_EXTENSION = ".tex";
    const LOG_EXTENSION = ".log";
    const PDF_EXTENSION = ".pdf";

    private string $tmpDir;

    /**
     * @param Environment $twig
     * @param LoggerInterface $logger
     * @param RendererRepository $rendererRepository
     * @param FileNameManager $fileNameManager
     * @param LatexCompiler $latexCompiler
     * @param PdfMerger $pdfMerger
     * @param int $compilationTimeLimitSeconds
     * @throws LatexCompilerNotFoundException|PdfMergerNotFoundException
     * @see PdflatexLatexCompiler
     */
    public function __construct(
        private readonly Environment        $twig,
        private readonly LoggerInterface    $logger,
        private readonly RendererRepository $rendererRepository,
        private readonly FileNameManager    $fileNameManager, // TODO export that dependency out of the file
        private readonly LatexCompiler      $latexCompiler,
        private readonly PdfMerger          $pdfMerger,
        int                                 $compilationTimeLimitSeconds = 300,
    )
    {
        $this->tmpDir = realpath(sys_get_temp_dir());
        $this->logger->debug("Tmp dir : " . $this->tmpDir);

        if (!$this->latexCompiler->checkExists()) {
            throw new LatexCompilerNotFoundException($this->latexCompiler);
        }

        if (!$this->pdfMerger->checkExists()) {
            throw new PdfMergerNotFoundException($this->pdfMerger);
        }

        // Autorise un temps de compilation supérieur
        set_time_limit($compilationTimeLimitSeconds);
    }

    public function getFeuilleProfilContent(
        Graphique       $graphique,
        ReponseCandidat $reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $score,
        array           $profil,
    ): string
    {
        $renderer = $this->rendererRepository->fromIndex($graphique->renderer_index);

        $optionsEchelle = [];
        $idToScore = array();
        $idToProfil = array();

        /** @var EchelleGraphique $echelleGraphique */
        foreach ($graphique->echelles as $echelleGraphique) {
            $optionsEchelle[$echelleGraphique->id] = $echelleGraphique->options;

            $idToScore[$echelleGraphique->id] = $score[$echelleGraphique->echelle->nom_php];
            $idToProfil[$echelleGraphique->id] = $profil[$echelleGraphique->echelle->nom_php];

        }
        return $renderer->render(
            environment: $this->twig,
            reponse: $reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            graphique: $graphique,
            score: $idToScore,
            profil: $idToProfil,
            options: $graphique->options,
            optionsEchelle: $optionsEchelle
        );
    }

    private
    function getWorkingDirPath(): string
    {
        return $this->tmpDir . DIRECTORY_SEPARATOR . time();
    }

    private
    function prepareOutputDir(): string|false
    {
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir);
        }

        $workingDirectoryPath = $this->getWorkingDirPath();

        $this->logger->debug("Working directory : " . $workingDirectoryPath);

        return mkdir($workingDirectoryPath) ? $workingDirectoryPath : false;
    }


    /**
     * @throws LatexCompilationFailedException
     */
    private
    function producePdfAndGetPath(Graphique       $graphique,
                                  ReponseCandidat $reponseCandidat,
                                  Correcteur      $correcteur,
                                  Etalonnage      $etalonnage,
                                  array           $score,
                                  array           $profil,
                                  string          $outputDirectoryPath,
                                  string          $fileNameWithoutExtension): string
    {
        $content = $this->getFeuilleProfilContent(
            graphique: $graphique,
            reponse: $reponseCandidat,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $score,
            profil: $profil
        );

        $filePathWithoutExtension = $outputDirectoryPath . DIRECTORY_SEPARATOR . $fileNameWithoutExtension;

        $texFilePath = $filePathWithoutExtension . self::TEX_EXTENSION;

        $file = fopen($texFilePath, 'w');
        fwrite($file, $content);
        fclose($file);

        $this->logger->debug("Wrote to file " . $texFilePath);

        $process = $this->latexCompiler->compilerProcess($outputDirectoryPath, $texFilePath);

        $this->logger->info("Execution du process de compilation", ["process" => $process, "cl" => $process->getCommandLine()]);

        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->critical("Echec de la compilation", ["stdout" => $process->getErrorOutput()]);
        }

        $filePath = $filePathWithoutExtension . self::PDF_EXTENSION;

        if (file_exists($filePath)) {
            $this->logger->info("Compilation réussie", ["path" => $filePath]);
            return $filePath;
        } else {
            $logFilePath = $filePathWithoutExtension . self::LOG_EXTENSION;

            if (file_exists($logFilePath)) {
                $this->logger->critical("Compilation échouée, fichier de log produit", ["log_path" => $logFilePath]);
                throw new LatexCompilationFailedException($reponseCandidat, $logFilePath);
            } else {
                $this->logger->critical("Compilation échouée, pas de fichier de log");
                throw new LatexCompilationFailedException($reponseCandidat, null);
            }
        }
    }

    private
    function produceResponse(string $filePath, string $outputFileNameWithExtension): BinaryFileResponse
    {
        $result = new BinaryFileResponse($filePath);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $outputFileNameWithExtension
        );
        return $result;
    }


    /**
     * @throws LatexCompilationFailedException
     */
    public
    function createPdfFile(
        Graphique       $graphique,
        ReponseCandidat $reponseCandidat,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $score,
        array           $profil): BinaryFileResponse|false
    {

        if ($outputDirectoryPath = $this->prepareOutputDir()) {

            $fileNameWithoutExtension = $this->fileNameManager->singlePdfFileName($reponseCandidat);

            $filePath = $this->producePdfAndGetPath(
                graphique: $graphique,
                reponseCandidat: $reponseCandidat,
                correcteur: $correcteur,
                etalonnage: $etalonnage,
                score: $score,
                profil: $profil,
                outputDirectoryPath: $outputDirectoryPath,
                fileNameWithoutExtension: $fileNameWithoutExtension
            );

            return $this->produceResponse(
                filePath: $filePath,
                outputFileNameWithExtension: $fileNameWithoutExtension . self::PDF_EXTENSION
            );
        }

        return false;
    }

    /**
     * @param Session $session la session commune à toutes les réponses. Toutes les réponses ne sont pas forcément incluses
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param array $scores
     * @param array $profils
     * @param Graphique $graphique
     * @param ReponseCandidat[] $reponsesCandidat
     * @return BinaryFileResponse|false
     * @throws LatexCompilationFailedException
     */
    public
    function createZipFile(
        Session    $session,
        Correcteur $correcteur,
        Etalonnage $etalonnage,
        array      $scores,
        array      $profils,
        Graphique  $graphique,
        array      $reponsesCandidat): BinaryFileResponse|false
    {
        if ($outputDirectoryPath = $this->prepareOutputDir()) {

            $zipFilePath = $outputDirectoryPath . DIRECTORY_SEPARATOR . self::TEMP_ZIP_FILE_NAME;

            $zip = new ZipArchive();
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var ReponseCandidat $reponses_candidat */
            foreach ($reponsesCandidat as $reponseCandidat) {

                $fileNameWithoutExtension = $this->fileNameManager->singlePdfFileName($reponseCandidat);

                $pdfFilePath = $this->producePdfAndGetPath(
                    graphique: $graphique,
                    reponseCandidat: $reponseCandidat,
                    correcteur: $correcteur,
                    etalonnage: $etalonnage,
                    score: $scores[$reponseCandidat->id],
                    profil: $profils[$reponseCandidat->id],
                    outputDirectoryPath: $outputDirectoryPath,
                    fileNameWithoutExtension: $fileNameWithoutExtension
                );

                $zip->addFile($pdfFilePath, $fileNameWithoutExtension . self::PDF_EXTENSION);
            }

            $zip->close();

            return $this->produceResponse($zipFilePath, $this->fileNameManager->mergedProfilsZipFileName($session));

        }

        return false;
    }

    /**
     * @param Session $session la session commune à toutes les réponses. Toutes les réponses ne sont pas forcément incluses
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param array $scores
     * @param array $profils
     * @param Graphique $graphique
     * @param ReponseCandidat[] $reponsesCandidat
     * @return BinaryFileResponse|false
     * @throws LatexCompilationFailedException
     */
    public
    function createPdfMergedFile(
        Session    $session,
        Correcteur $correcteur,
        Etalonnage $etalonnage,
        array      $scores,
        array      $profils,
        Graphique  $graphique,
        array      $reponsesCandidat): BinaryFileResponse|false
    {
        if ($outputDirectoryPath = $this->prepareOutputDir()) {

            $mergedPdfFilePath = $outputDirectoryPath . DIRECTORY_SEPARATOR . self::MERGED_PDF_FILE_NAME;

            $pdfFilePaths = [];

            /** @var ReponseCandidat $reponses_candidat */
            foreach ($reponsesCandidat as $reponseCandidat) {

                $fileNameWithoutExtension = $this->fileNameManager->singlePdfFileName($reponseCandidat);

                $pdfFilePath = $this->producePdfAndGetPath(
                    graphique: $graphique,
                    reponseCandidat: $reponseCandidat,
                    correcteur: $correcteur,
                    etalonnage: $etalonnage,
                    score: $scores[$reponseCandidat->id],
                    profil: $profils[$reponseCandidat->id],
                    outputDirectoryPath: $outputDirectoryPath,
                    fileNameWithoutExtension: $fileNameWithoutExtension
                );

                $pdfFilePaths[] = $pdfFilePath;

            }

            $process = $this->pdfMerger->mergerProcess($pdfFilePaths, $mergedPdfFilePath);
            $this->logger->info("Process de fusion des pdf", ["process" => $process, "cl" => $process->getCommandLine()]);

            $process->run();

            if (!$process->isSuccessful()) {
                $this->logger->critical("Echec du process de fusion des pdf", ["stdout" => $process->getErrorOutput()]);
            }


            if (!file_exists($mergedPdfFilePath)) {
                $this->logger->error("Echec de la fusion des pdf");
                return false;
            }

            return $this->produceResponse($mergedPdfFilePath, $this->fileNameManager->mergedProfilsPdfFileName($session));
        }

        return false;
    }
}
