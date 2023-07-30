<?php

namespace App\Tests\Core\Files\Csv\Reponses;

use App\Core\ReponseCandidat\ExportReponsesCandidat;
use App\Core\ReponseCandidat\ImportReponsesCandidat;
use App\Entity\ReponseCandidat;
use App\Repository\SessionRepository;
use ArrayIterator;
use Exception;
use MultipleIterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReponsesCandidatImportTest extends KernelTestCase
{

    /**
     * @throws Exception
     */
    public function testImportExport()
    {

        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);

        $reponsesCandidatExport = self::getContainer()->get(ExportReponsesCandidat::class);
        $reponsesCandidatImport = self::getContainer()->get(ImportReponsesCandidat::class);

        $exported = $reponsesCandidatExport->export($session->reponses_candidats->toArray());
        $reimported = $reponsesCandidatImport->import($session, $exported);

        $multipleIterator = new MultipleIterator();
        $multipleIterator->attachIterator(new ArrayIterator($session->reponses_candidats->toArray()));
        $multipleIterator->attachIterator(new ArrayIterator($reimported));

        foreach ($multipleIterator as $both) {

            /** @var ReponseCandidat $expected */
            $expected = $both[0];
            /** @var ReponseCandidat $actual */
            $actual = $both[1];

            self::assertEquals($expected->nom, $actual->nom);
            self::assertEquals($expected->prenom, $actual->prenom);
            self::assertEquals($expected->nom_jeune_fille, $actual->nom_jeune_fille);
            self::assertEquals($expected->niveau_scolaire, $actual->niveau_scolaire);
            self::assertEqualsWithDelta($expected->date_de_naissance, $actual->date_de_naissance, 86400);
            self::assertEquals($expected->sexe, $actual->sexe);
            self::assertEquals($expected->reserve, $actual->reserve);
            self::assertEquals($expected->autre_1, $actual->autre_1);
            self::assertEquals($expected->autre_2, $actual->autre_2);
            self::assertEquals($expected->code_barre, $actual->code_barre);
            self::assertEquals($expected->eirs, $actual->eirs);
            self::assertEqualsCanonicalizing($expected->reponses, $actual->reponses);
            // TODO other fields ?
        }

    }
}
