<?php

namespace App\Core\Files\Csv;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvManager
{
    const CSV_TMP_FILE_NAME = "tmp/tmp.csv";

    public function export(array $data, string $file_name): BinaryFileResponse
    {
        $encoder = new CsvEncoder();

        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);

        $tmp = fopen(self::CSV_TMP_FILE_NAME, 'w+');
        fwrite($tmp, $encoded);
        fclose($tmp);

        $result = new BinaryFileResponse(self::CSV_TMP_FILE_NAME);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file_name
        );

        return $result;
    }
}