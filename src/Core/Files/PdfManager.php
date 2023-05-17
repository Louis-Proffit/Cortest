<?php

namespace App\Core\Files;

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

class PdfManager
{

    const MERGED_PDF_FILE_NAME = "merged.pdf";
    const TEMP_ZIP_FILE_NAME = "temp.zip";

    const ZIP_EXTENSION = ".zip";
    const TEX_EXTENSION = ".tex";
    const PDF_EXTENSION = ".pdf";

    private string $tmp_dir;

    public function __construct(
        private readonly Environment        $twig,
        private readonly LoggerInterface    $logger,
        private readonly RendererRepository $renderer_repository,
        private readonly FileNameManager    $fileNameManager, // TODO export that dependency out of the file
        private readonly string             $latexCompilerExecutable = "pdflatex",
        private readonly string             $pdfMergeExecutable = "pdfunite",
        int                                 $new_time_limit = 300
    )
    {
        $this->tmp_dir = sys_get_temp_dir();
        $this->logger->debug("Tmp dir : " . $this->tmp_dir);

        // Autorise un temps de compilation supérieur
        set_time_limit($new_time_limit);
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

    /*private function deleteFolderWithContent(string $dir): void
    {
        $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }*/

    private function createTempDirIfNotExists(): void
    {
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir);
        }
    }

    private function getOutputDirPath(): string
    {
        return $this->tmp_dir . DIRECTORY_SEPARATOR . time();
    }

    private function getTempMergedPdfFilePath(string $outputDirectoryPath): string
    {
        return $outputDirectoryPath . DIRECTORY_SEPARATOR . self::MERGED_PDF_FILE_NAME;
    }

    private function getTempZipFilePath(string $outputDirectoryPath): string
    {
        return $outputDirectoryPath . DIRECTORY_SEPARATOR . self::TEMP_ZIP_FILE_NAME;
    }

    private function addExtensionToFile(string $fileName, string $extension): string
    {
        return $fileName . $extension;
    }

    private function prepareOutputDir(): string|false
    {
        $this->createTempDirIfNotExists();

        $outputDirectoryPath = $this->getOutputDirPath();

        $this->logger->debug("Output directory : " . $outputDirectoryPath);

        if (mkdir($outputDirectoryPath)) {

            return $outputDirectoryPath;

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
        return $this->pdfMergeExecutable . " " . implode(" ", $toMerge) . " " . $output;
    }

    private function buildCompileTexToPdfCommand(string $outputDirectory, string $texFileName): string
    {
        return $this->latexCompilerExecutable . " --output-directory=\"" . $outputDirectory . "\" \"" . $texFileName . "\"";
    }


    private function producePdfAndGetPath(Graphique       $graphique,
                                          ReponseCandidat $candidat_reponse,
                                          Correcteur      $correcteur,
                                          Etalonnage      $etalonnage,
                                          array           $score,
                                          array           $profil,
                                          string          $outputDirectoryPath,
                                          string          $fileNameWithoutExtension): string
    {
        $content = $this->getFeuilleProfilContent(
            graphique: $graphique,
            reponse: $candidat_reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $score,
            profil: $profil
        );

        $filePathWithoutExtension = $outputDirectoryPath . DIRECTORY_SEPARATOR . $fileNameWithoutExtension;

        $texFilePath = $this->addExtensionToFile($filePathWithoutExtension, self::TEX_EXTENSION);

        $file = fopen($texFilePath, 'w');
        fwrite($file, $content);
        fclose($file);

        $this->logger->debug("Wrote to file " . $texFilePath);

        $command = $this->buildCompileTexToPdfCommand($outputDirectoryPath, $texFilePath);
        $this->logger->debug("Executing command : " . $command);
        exec($command);

        return $this->addExtensionToFile($filePathWithoutExtension, self::PDF_EXTENSION);
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
                candidat_reponse: $reponseCandidat,
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

            $zipFilePath = $this->getTempZipFilePath($outputDirectoryPath);

            $zip = new ZipArchive();
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var ReponseCandidat $reponses_candidat */
            foreach ($reponsesCandidat as $reponseCandidat) {

                $fileNameWithoutExtension = $this->fileNameManager->singlePdfFileName($reponseCandidat);

                $pdfFilePath = $this->producePdfAndGetPath(
                    graphique: $graphique,
                    candidat_reponse: $reponseCandidat,
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

            $mergedPdfFilePath = $this->getTempMergedPdfFilePath($outputDirectoryPath);

            $pdfFilePaths = [];

            /** @var ReponseCandidat $reponses_candidat */
            foreach ($reponsesCandidat as $reponseCandidat) {

                $fileNameWithoutExtension = $this->fileNameManager->singlePdfFileName($reponseCandidat);

                $pdfFilePath = $this->producePdfAndGetPath(
                    graphique: $graphique,
                    candidat_reponse: $reponseCandidat,
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
