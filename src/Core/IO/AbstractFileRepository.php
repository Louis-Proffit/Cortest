<?php

namespace App\Core\IO;

use App\Core\Exception\MissingFileException;
use App\Core\Exception\UploadFailException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


readonly abstract class AbstractFileRepository
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
            $this->logger->info("Le dossier n'existe pas, création", ["dir" => $dir]);
            $this->filesystem->mkdir($dir);
        }
    }

    /**
     * @param UploadedFile $file
     * @param $entity
     * @throws UploadFailException
     */
    public function upload(UploadedFile $file, $entity): void
    {
        try {
            $fileName = $this->entityFileName($entity);
            $this->logger->info("Upload du fichier", ["fileName" => $fileName]);
            $file->move($this->dir, $fileName);
        } catch (FileException $exception) {
            throw new UploadFailException($entity, throwable: $exception);
        }
    }

    public function delete($entity): void
    {
        $this->logger->info("Upload du fichier", ["fileName" => $this->entityFilePath($entity)]);
        $this->filesystem->remove($this->entityFilePath($entity));
    }


    public function entityFilePathOrNull($entity): string|null
    {
        $filePath = $this->entityFilePath($entity);
        if ($this->filesystem->exists($filePath)) {
            return $filePath;
        } else {
            return null;
        }
    }

    /**
     * @throws MissingFileException
     */
    public function entityFileContent($entity): string
    {
        $filePath = $this->entityFilePath($entity);
        if ($this->filesystem->exists($filePath)) {
            return file_get_contents($filePath);
        } else {
            throw new MissingFileException($entity);
        }
    }

    /**
     * @param mixed $entity
     * @return string
     */
    protected function entityFilePath(mixed $entity): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->entityFileName($entity);
    }


    /**
     * @param mixed $entity
     * @return string
     */
    protected abstract function entityFileName(mixed $entity): string;
}