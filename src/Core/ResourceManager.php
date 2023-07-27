<?php

namespace App\Core;

use App\Entity\Resource;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ResourceManager
{


    private string $dir;


    public function __construct(
        private Filesystem      $filesystem,
        private LoggerInterface $logger,
        string                  $dir
    )
    {

        $this->dir = $dir;
        if (!$this->filesystem->exists($dir)) {
            $this->logger->info("Le dossier de resources n'existe pas, création");
            $this->filesystem->mkdir($dir);
        }
    }

    public function upload(UploadedFile $file, Resource $resource): void
    {
        $fileName = $this->resourceFileName($resource);
        $this->logger->info("Upload du fichier", ["fileName" => $fileName]);
        $file->move($this->dir, $fileName);
    }


    public function delete(Resource $resource): void
    {
        $this->logger->info("Upload du fichier", ["fileName" => $this->resourceFileName($resource)]);
        $this->filesystem->remove($this->resourceFilePath($resource));
    }

    public function resourceFilePath(Resource $resource): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->resourceFileName($resource);
    }

    private function resourceFileName(Resource $resource): string
    {
        return "" . $resource->id;
    }
}