<?php

namespace App\Core\Files\Csv;

use App\Core\Files\PdfManager;
use SplFileObject;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvManager
{
    const CSV_TMP_DIRECTORY = "tmp";
    const SEPARATOR = "/";
    const CSV_TMP_FILE_NAME = "tmp.csv";
    const CSV_TMP_LOCAL_PATH = self::CSV_TMP_DIRECTORY . self::SEPARATOR . self::CSV_TMP_FILE_NAME;

    private string $tmp_dir;

    public function __construct(string $tmp_dir = "tmp")
    {
        $this->tmp_dir = getcwd() . PdfManager::SEPARATOR . $tmp_dir;
    }

    public function export(array $data, string $file_name): BinaryFileResponse
    {
        $encoder = new CsvEncoder();
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir);
        }

        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);

        $tmp = fopen(self::CSV_TMP_LOCAL_PATH, 'w+');
        fwrite($tmp, $encoded);
        fclose($tmp);

        $result = new BinaryFileResponse(self::CSV_TMP_LOCAL_PATH);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file_name
        );

        return $result;
    }

    /**
     * Produit un array associatif.
     * La première ligne du fichier doit être un en-tête de colonne
     * Les autre lignes sont des données, dont les clés sont les en-têtes de colonne
     * @param string $filePath le chemin absolu vers le fichier
     * @return array
     */
    public function import(string $filePath): array
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $rows = [];

        foreach ($file as $row) {
            if (gettype($row) === "array") {
                $rows[] = $row;
            }
        }

        $columnNames = $rows[0];
        $associated_rows = [];

        foreach ($rows as $index => $row) {
            if ($index > 0) {
                $associated_rows[] = array_combine($columnNames, $row);
            }
        }

        return $associated_rows;
    }
}