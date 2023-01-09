<?php

namespace App\Core\Pdf;

use App\Entity\CandidatReponse;
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
        private readonly LoggerInterface $logger
    )
    {
    }

    private function fileName(CandidatReponse $candidat_reponse): string
    {
        return $candidat_reponse->raw["nom"] . "_" . $candidat_reponse->raw["prenom"];
    }

    private function outputZipName(Session $session): string
    {
        return "Profils_session_" . $session->date->format("Y-m-d");
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getFeuilleProfil(string $template, array $data): string
    {
        return $this->twig->render($template, $data);
    }

    private function deleteFolderWithContent(string $dir)
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

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function createZipFile(Session $session, string $template): BinaryFileResponse|false
    {
        $tmpFolder = "tmp";

        if (!dir($tmpFolder)) {
            mkdir($tmpFolder);
        }

        $this->logger->debug("Temp path : " . $tmpFolder);

        $outputDirectoryPath = $tmpFolder . "\\" . microtime();

        $this->logger->debug("Output directory : " . $outputDirectoryPath);

        if (mkdir($outputDirectoryPath)) {

            $zip = new ZipArchive();
            $zip->open($outputDirectoryPath . "\\temp.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

            /** @var CandidatReponse $reponses_candidat */
            foreach ($session->reponses_candidats as $reponses_candidat) {

                $content = $this->getFeuilleProfil($template, $reponses_candidat->raw); // TODO add score and profil

                $fileName = $this->fileName($reponses_candidat);

                $filePathWithoutExtension = $outputDirectoryPath . "\\" . $fileName;

                $file = fopen($filePathWithoutExtension . ".tex", 'w');
                fwrite($file, $content);
                fclose($file);

                $command = "pdflatex --output-directory=" . $outputDirectoryPath . " " . $filePathWithoutExtension . ".tex";
                $this->logger->debug("Executing command : " . $command);
                exec($command);

                $zip->addFile($filePathWithoutExtension . ".pdf", $fileName . ".pdf");
            }

            $zip->close();

            $result = new BinaryFileResponse($outputDirectoryPath . "\\temp.zip");
            $result->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $this->outputZipName($session) . ".zip"
            );

            $this->deleteFolderWithContent($outputDirectoryPath);

            return $result;

        } else {
            return false;
        }
    }

}