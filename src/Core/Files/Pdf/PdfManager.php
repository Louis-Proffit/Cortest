<?php

namespace App\Core\Files\Pdf;

use App\Core\Files\FileNameManager;
use App\Core\Renderer\RendererRepository;
use App\Entity\Correcteur;
use App\Entity\EchelleGraphique;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\ReponseCandidat;
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

    private string $tmp_dir;

    public function __construct(
        private readonly Environment        $twig,
        private readonly LoggerInterface    $logger,
        private readonly RendererRepository $renderer_repository,
        private readonly FileNameManager    $fileNameManager, // TODO export that dependency out of the file
        private readonly string             $latexCompilerExecutable = "pdflatex",
        private readonly string             $latexCommandLineOptions = "-interaction=nonstopmode -file-line-error",
        private readonly string             $pdfMergeExecutable = "pdfunite",
        int                                 $compilationTimeLimitSeconds = 300,
    )
    {
        $this->tmp_dir = sys_get_temp_dir();
        $this->logger->debug("Tmp dir : " . $this->tmp_dir);

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
        $renderer = $this->renderer_repository->fromIndex($graphique->renderer_index);

        $optionsEchelle = [];
        $score_for_id = array();
        $profil_for_id = array();

        /** @var EchelleGraphique $echelleGraphique */
        foreach ($graphique->echelles as $echelleGraphique) {
            $optionsEchelle[$echelleGraphique->id] = $echelleGraphique->options;

            $score_for_id[$echelleGraphique->id] = $score[$echelleGraphique->echelle->nom_php];
            $profil_for_id[$echelleGraphique->id] = $profil[$echelleGraphique->echelle->nom_php];

        }
        return $renderer->render(
            environment: $this->twig,
            reponse: $reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            graphique: $graphique,
            score: $score_for_id,
            profil: $profil_for_id,
            options: $graphique->options,
            optionsEchelle: $optionsEchelle
        );
    }

    private function getWorkingDirPath(): string
    {
        return $this->tmp_dir . DIRECTORY_SEPARATOR . time();
    }

    private function prepareOutputDir(): string|false
    {
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir);
        }

        $workingDirectoryPath = $this->getWorkingDirPath();

        $this->logger->debug("Working directory : " . $workingDirectoryPath);

        if (mkdir($workingDirectoryPath)) {

            return $workingDirectoryPath;

        } else {

            return false;

        }
    }

    /**
     * @param string[] $toMerge files to merge
     * @param string $output output to produce
     * @return string the runnable command
     */
    private function buildMergePdfCommand(array $toMerge, string $output): string
    {
        $quotedFileNames = array_map(fn(string $name) => "\"" . $name . "\"", $toMerge);
        return $this->pdfMergeExecutable . " " . implode(" ", $quotedFileNames) . " \"" . $output . "\"";
    }

    private function buildCompileTexToPdfCommand(string $outputDirectory, string $texFileName): string
    {
        return $this->latexCompilerExecutable . " " . $this->latexCommandLineOptions . " --output-directory=\"" . $outputDirectory . "\" \"" . $texFileName . "\"";
    }


    /**
     * @throws LatexCompilationFailedException
     */
    private function producePdfAndGetPath(Graphique       $graphique,
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

        $command = $this->buildCompileTexToPdfCommand($outputDirectoryPath, $texFilePath);
        $this->logger->debug("Executing command : " . $command);
        exec($command);

        $filePath = $filePathWithoutExtension . self::PDF_EXTENSION;

        if (file_exists($filePath)) {
            return $filePath;
        } else {
            $logFilePath = $filePathWithoutExtension . self::LOG_EXTENSION;

            if (file_exists($logFilePath)) {
                throw new LatexCompilationFailedException($reponseCandidat, $logFilePath);
            } else {
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
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param array $scores
     * @param array $profils
     * @param Graphique $graphique
     * @param ReponseCandidat[] $reponsesCandidat
     * @return BinaryFileResponse|false
     * @throws LatexCompilationFailedException
     */
    public function createZipFile(
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

            return $this->produceResponse($zipFilePath, $this->fileNameManager->mergedProfilsZipFileName($reponsesCandidat));

        }

        return false;
    }

    /**
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     * @param array $scores
     * @param array $profils
     * @param Graphique $graphique
     * @param ReponseCandidat[] $reponsesCandidat
     * @return BinaryFileResponse|false
     * @throws LatexCompilationFailedException
     */
    public function createPdfMergedFile(
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

            $command = $this->buildMergePdfCommand($pdfFilePaths, $mergedPdfFilePath);

            $this->logger->info("Merging pdf files with command : " . $command);

            exec($command);

            if (!file_exists($mergedPdfFilePath)) {
                $this->logger->error("Failed to produce merged pdf file");
                return false;
            }

            return $this->produceResponse($mergedPdfFilePath, $this->fileNameManager->mergedProfilsPdfFileName($reponsesCandidat));
        }

        return false;
    }
}
