<?php

namespace App\Core\Files\Csv;

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


    public function export(array $data, string $file_name): BinaryFileResponse
    {
        $encoder = new CsvEncoder();

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

    public function import(string $filePath): array
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $rows = [];
        foreach ($file as $row) {
            if (gettype($row) === "array"){
                $rows[] = $row;
            }
        }

        return $rows;
    }
}