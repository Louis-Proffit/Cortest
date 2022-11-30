<?php

namespace App\Tests;

use App\Core\Pdf\PdfMaker;
use ReponseBrigadierDePolice;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class TestSerDeser extends TestCase
{

    public function testSerDeser()
    {
        $serializer = SerializerBuilder::create()->build();

        echo getcwd();
        require_once "../public/res/def_grille/ReponseBrigadierDePolice.php";
        $grille = new ReponseBrigadierDePolice();

        $grille->fill("");

        $ser = $serializer->serialize($grille, "json");

        echo $ser;

        $deser = $serializer->deserialize($ser, ReponseBrigadierDePolice::class, "json");

        dump($deser);

        self::assertEquals($grille, $deser);
    }
}
