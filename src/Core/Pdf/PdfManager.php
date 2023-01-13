<?php

namespace App\Core\Pdf;

use App\Core\ProfilGraphique\ProfilGraphique;
use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use FilesystemIterator;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use ZipArchive;

class PdfManager
{

    public function __construct(
        private readonly Environment     $twig,
        private readonly LoggerInterface $logger,
        private readonly string          $latexCompilerExecutable = "pdflatex",
        private readonly string          $tmp_dir = "tmp"
    )
    {
    }

    private function fileName(ReponseCandidat $candidat_reponse): string
    {
        return $candidat_reponse->raw["nom"] . "_" . $candidat_reponse->raw["prenom"];
    }

    private function outputZipFileName(Session $session): string
    {
        return "Profils_Session_" . $session->date->format("Y-m-d") . ".zip";
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getFeuilleProfilContent(
        ProfilGraphique $profil_graphique,
        ReponseCandidat $candidat_reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $score,
        array           $profil,
    ): string
    {

        return $this->twig->render(
            $profil_graphique->getTemplate(),
            [
                "session" => $candidat_reponse->session,
                "reponse" => $candidat_reponse,
                "score" => $score,
                "profil" => $profil,
                "etalonnage" => $etalonnage,
                "correcteur" => $correcteur
            ]);
    }

    private function deleteFolderWithContent(string $dir): void
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
    }

    private function createTempDirIfNotExists(): void
    {
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir);
        }
    }

    private function getOutputDirPath(): string
    {
        return $this->tmp_dir . "\\" . time();
    }

    private function getTempZipFilePath(string $outputDirectoryPath): string
    {
        return $outputDirectoryPath . "\\temp.zip";
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

    private function buildCommand(string $outputDirectory, string $texFileName): string
    {
        return $this->latexCompilerExecutable . " --output-directory=" . $outputDirectory . " " . $texFileName;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function producePdfAndGetPath(ProfilGraphique $profil_graphique,
                                          ReponseCandidat $candidat_reponse,
                                          Correcteur      $correcteur,
                                          Etalonnage      $etalonnage,
                                          array           $score,
                                          array           $profil,
                                          string          $outputDirectoryPath,
                                          string          $fileNameWithoutExtension): string
    {
        $content = $this->getFeuilleProfilContent(
            profil_graphique: $profil_graphique,
            candidat_reponse: $candidat_reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            score: $score,
            profil: $profil
        );

        $filePathWithoutExtension = $outputDirectoryPath . "\\" . $fileNameWithoutExtension;

        $texFilePath = $this->addExtensionToFile($filePathWithoutExtension, ".tex");

        $file = fopen($texFilePath, 'w');
        fwrite($file, $content);
        fclose($file);

        $command = $this->buildCommand($outputDirectoryPath, $texFilePath);
        $this->logger->debug("Executing command : " . $command);
        exec($command);

        return $this->addExtensionToFile($filePathWithoutExtension, ".pdf");
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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public
    function createPdfFile(
        ProfilGraphique $profil_graphique,
        ReponseCandidat $candidat_reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $score,
        array           $profil): BinaryFileResponse|false
    {

        if ($outputDirectoryPath = $this->prepareOutputDir()) {
            $fileNameWithoutExtension = $this->fileName($candidat_reponse);
            $filePath = $this->producePdfAndGetPath(
                profil_graphique: $profil_graphique,
                candidat_reponse: $candidat_reponse,
                correcteur: $correcteur,
                etalonnage: $etalonnage,
                score: $score,
                profil: $profil,
                outputDirectoryPath: $outputDirectoryPath,
                fileNameWithoutExtension: $fileNameWithoutExtension
            );

            return $this->produceResponse(
                filePath: $filePath,
                outputFileNameWithExtension: $fileNameWithoutExtension . ".pdf"
            );
        }

        return false;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public
    function createZipFile(
        Session         $session,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $scores,
        array           $profils,
        ProfilGraphique $profil_graphique): BinaryFileResponse|false
    {
        if ($outputDirectoryPath = $this->prepareOutputDir()) {

            $zipFilePath = $this->getTempZipFilePath($outputDirectoryPath);

            $zip = new ZipArchive();
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var ReponseCandidat $reponses_candidat */
            foreach ($session->reponses_candidats as $reponses_candidat) {

                $fileNameWithoutExtension = $this->fileName($reponses_candidat);

                $pdfFilePath = $this->producePdfAndGetPath(
                    profil_graphique: $profil_graphique,
                    candidat_reponse: $reponses_candidat,
                    correcteur: $correcteur,
                    etalonnage: $etalonnage,
                    score: $scores[$reponses_candidat->id],
                    profil: $profils[$reponses_candidat->id],
                    outputDirectoryPath: $outputDirectoryPath,
                    fileNameWithoutExtension: $fileNameWithoutExtension
                );

                $zip->addFile($pdfFilePath, $fileNameWithoutExtension . ".pdf");
            }

            $zip->close();

            return $this->produceResponse($zipFilePath, $this->outputZipFileName($session));

        }

        return false;
    }
}