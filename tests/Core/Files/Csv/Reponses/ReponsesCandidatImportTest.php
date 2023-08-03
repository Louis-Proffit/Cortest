<?php

namespace App\Tests\Core\Files\Csv\Reponses;

use App\Core\ReponseCandidat\ExportReponseCandidat;
use App\Core\ReponseCandidat\ExportReponsesCandidat;
use App\Core\ReponseCandidat\ImportReponseCandidat;
use App\Core\ReponseCandidat\ImportReponsesCandidat;
use App\Entity\Concours;
use App\Entity\NiveauScolaire;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Test;
use App\Repository\GrilleRepository;
use App\Repository\ReponseCandidatRepository;
use App\Repository\SessionRepository;
use App\Tests\DoctrineTestTrait;
use ArrayIterator;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use MultipleIterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function Sodium\add;

class ReponsesCandidatImportTest extends KernelTestCase
{
    use DoctrineTestTrait;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $entityManager = $this->getEntityManager();


        $concours = new Concours(id: 0, nom: "concours", type_concours: 1, tests: new ArrayCollection());
        $test = new Test(
            id: 0,
            nom: "test",
            version_batterie: 1,
            index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            concours: new ArrayCollection([$concours]),
            correcteurs: new ArrayCollection(),
            sessions: new ArrayCollection(),
            questions: new ArrayCollection()
        );
        $concours->tests->add($test);

        for ($i = 1; $i <= 640; $i++) {
            $test->questions->add(new QuestionTest(
                id: 0,
                indice: $i,
                intitule: "Q" . $i,
                abreviation: "q" . $i,
                test: $test,
                type: QuestionTest::TYPE_INUTILISE
            ));
        }

        $sgap = new Sgap(id: 0, indice: 1, nom: "sgap", sessions: new ArrayCollection());

        $session = new Session(
            id: 0,
            date: new DateTime(),
            numero_ordre: 1,
            observations: "observations",
            test: $test,
            sgap: $sgap,
            reponses_candidats: new ArrayCollection()
        );

        $niveauScolaire = new NiveauScolaire(id: 0, indice: 0, nom: "nom");

        $reponseCandidat = new ReponseCandidat(
            id: 0,
            session: $session,
            reponses: array_fill(1, 640, "3"),
            nom: "nom",
            prenom: "prenom",
            nom_jeune_fille: "nom_jeune_fille",
            niveau_scolaire: $niveauScolaire,
            date_de_naissance: new DateTime(),
            sexe: ReponseCandidat::INDEX_HOMME,
            reserve: "",
            autre_1: "",
            autre_2: "",
            code_barre: 1,
            eirs: ReponseCandidat::TYPE_E,
            raw: null
        );

        $session->reponses_candidats->add($reponseCandidat);

        $test->sessions->add($session);
        $sgap->sessions->add($session);

        $entityManager->persist($sgap);
        $entityManager->persist($niveauScolaire);
        $entityManager->persist($test);
        $entityManager->persist($concours);
        $entityManager->persist($session);
        $entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function testImportExport()
    {

        $reponseCandidat = self::getContainer()->get(ReponseCandidatRepository::class)->findOneBy([]);
        $session = $reponseCandidat->session;

        $reponsesCandidatExport = self::getContainer()->get(ExportReponseCandidat::class);
        $reponsesCandidatImport = self::getContainer()->get(ImportReponseCandidat::class);

        $exported = $reponsesCandidatExport->exportCandidatAndReponses(
            reponseCandidat: $reponseCandidat,
            questions: $session->test->questions->toArray());
        $reimported = $reponsesCandidatImport->importReponse(
            session: $session,
            questions: $session->test->questions->toArray(),
            rawReponsesCandidat: $exported);

        self::assertEquals($reponseCandidat->nom, $reimported->nom);
        self::assertEquals($reponseCandidat->prenom, $reimported->prenom);
        self::assertEquals($reponseCandidat->nom_jeune_fille, $reimported->nom_jeune_fille);
        self::assertEquals($reponseCandidat->niveau_scolaire, $reimported->niveau_scolaire);
        self::assertEqualsWithDelta($reponseCandidat->date_de_naissance, $reimported->date_de_naissance, 86400);
        self::assertEquals($reponseCandidat->sexe, $reimported->sexe);
        self::assertEquals($reponseCandidat->reserve, $reimported->reserve);
        self::assertEquals($reponseCandidat->autre_1, $reimported->autre_1);
        self::assertEquals($reponseCandidat->autre_2, $reimported->autre_2);
        self::assertEquals($reponseCandidat->code_barre, $reimported->code_barre);
        self::assertEquals($reponseCandidat->eirs, $reimported->eirs);
        self::assertEqualsCanonicalizing($reponseCandidat->reponses, $reimported->reponses);

    }
}
