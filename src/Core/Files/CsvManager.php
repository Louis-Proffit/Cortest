<?php

namespace App\Core\Files;

use SplFileObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvManager
{

    /**
     * Produit une réponse http contenant un fichier à partir de ses données brutes et du nom du fichier
     * @param array $data
     * @param string $fileName
     * @return Response
     * @see CsvEncoder::encode()
     */
    public function export(array $data, string $fileName): Response
    {
        $encoder = new CsvEncoder();
        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);
        $response = new Response($encoded);
        FileUtils::setFileResponseFileName($response, $fileName);
        return $response;
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
            if (is_array($row)) {
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