<?php

namespace App\Tests\Core\Import;

use App\Core\Import\ImportCorrecteurXML;
use App\Entity\Correcteur;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImportCorrecteurXMLTest extends KernelTestCase
{

    /**
     * @throws Exception
     */
    public function testImport()
    {
        /** @var ImportCorrecteurXML $importCorrecteur */
        $importCorrecteur = self::getContainer()->get(ImportCorrecteurXML::class);

        $xml = file_get_contents(__DIR__ . "\\" . "import.xml");

        $correcteur = $importCorrecteur->load($xml);

        self::assertInstanceOf(Correcteur::class, $correcteur);

        dump($correcteur);
    }
}
