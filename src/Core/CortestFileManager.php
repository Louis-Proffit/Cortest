<?php

namespace App\Core;

use App\Core\Exception\UploadFailException;
use App\Entity\Resource;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @template T
 */
readonly abstract class CortestFileManager
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
     * @param T $entity
     * @return bool
     * @throws UploadFailException
     */
    public function upload(UploadedFile $file, mixed $entity): void
    {
        try {
            $fileName = $this->entityFileName($entity);
            $this->logger->info("Upload du fichier", ["fileName" => $fileName]);
            $file->move($this->dir, $fileName);
        } catch (FileException $exception) {
            throw new UploadFailException($entity, throwable: $exception);
        }
    }


    /**
     * @param T $entity
     * @return void
     */
    public function delete(Resource $entity): void
    {
        $this->logger->info("Upload du fichier", ["fileName" => $this->entityFilePath($entity)]);
        $this->filesystem->remove($this->entityFilePath($entity));
    }

    /**
     * @param T $entity
     * @return string|null
     */
    public function entityFilePathOrNull(mixed $entity): string|null
    {
        $filePath = $this->entityFilePath($entity);
        if ($this->filesystem->exists($filePath)) {
            return $filePath;
        } else {
            return null;
        }
    }

    /**
     * @param T $entity
     * @return string
     */
    protected function entityFilePath(mixed $entity): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . $this->entityFileName($entity);
    }


    /**
     * @param T $entity
     * @return string
     */
    protected abstract function entityFileName(mixed $entity): string;
}