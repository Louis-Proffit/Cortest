<?php

namespace App\Fixture\Bpmr;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Entity\NiveauScolaire;
use App\Entity\Structure;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Test;
use App\Fixture\InitFixture;
use App\Repository\GrilleRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractBpmrFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function __construct(
        private readonly string $type_concours,
        private readonly string $version_batterie,
        private readonly int    $nombre_questions,
        private readonly string $concours_nom,
        private readonly string $structure_nom,
        private readonly string $correcteur_nom,
        private readonly string $etalonnage_nom,
        private readonly int    $etalonnage_nombre_classes,
        private readonly string $test_nom,
    )
    {
    }

    public static function getGroups(): array
    {
        return ["bpmr"];
    }

    public function getDependencies(): array
    {
        return [InitFixture::class];
    }

    public function load(ObjectManager $manager): void
    {
        // Session concours score_etalonne --------------------------------
        $concours = new Concours(
            0,
            $this->concours_nom,
            $this->type_concours,
            new ArrayCollection(),
            new ArrayCollection(),
        );

        $test = new Test(
            id: 0,
            nom: $this->test_nom,
            version_batterie: $this->version_batterie,
            index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            concours: new ArrayCollection(),
            correcteurs: new ArrayCollection(),
            sessions: new ArrayCollection(),
            questions: new ArrayCollection(),
        );

        $test->concours->add($concours);

        $session = $this->sessionExemple(
            $test,
            $manager->getRepository(Sgap::class)->findOneBy([]),
            $manager->getRepository(NiveauScolaire::class)->findOneBy([]),
            $concours,
        );

        $this->questions($test);

        $structure = new Structure(
            0,
            $this->structure_nom,
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection()
        );

        $this->aptitudesCognitives($structure);
        $this->personnalite($structure);

        $manager->persist($test);
        $manager->persist($concours);
        $manager->persist($structure);
        $manager->persist($session);
        $manager->flush();

        // CORRECTEUR -----------------------
        $correcteur = new Correcteur(
            0,
            new ArrayCollection([$test]),
            $structure,
            $this->correcteur_nom,
            new ArrayCollection()
        );

        $manager->persist($correcteur);
        $this->correcteurPersonnalite($structure, $correcteur);
        $this->correcteurAptitudesCognitives($structure, $correcteur);

        // ETALONNAGE --------------------------

        $etalonnage = new Etalonnage(
            0,
            $structure,
            $this->etalonnage_nom,
            $this->etalonnage_nombre_classes,
            new ArrayCollection()
        );

        foreach ($structure->echelles as $echelle) {
                $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle($echelle,
                $etalonnage,
                $this->etalonnage_nombre_classes));
        }

        $manager->persist($etalonnage);
        $manager->flush();
    }

    private function sessionExemple(Test $test, Sgap $sgap, NiveauScolaire $niveau_scolaire, Concours $concours): Session
    {
        $session = new Session(
            0,
            new DateTime("now"),
            0,
            "Pas d'observations",
            $test,
            $sgap,
            new ArrayCollection(),
            $concours,
        );

        $reponses = array_fill(1, $this->nombre_questions, 3);

        foreach (range(1, 20) as $index) {
            $session->reponses_candidats->add(new ReponseCandidat(
                id: 0,
                session: $session,
                reponses: $reponses,
                nom: "NOM " . $index,
                prenom: "PRENOM " . $index,
                nom_jeune_fille: "Sans objet",
                niveau_scolaire: $niveau_scolaire,
                date_de_naissance: new DateTime("now"),
                sexe: ReponseCandidat::INDEX_HOMME,
                reserve: "RESERVE TEST",
                autre_1: "AUTRE 1",
                autre_2: "AUTRE 2",
                code_barre: 1,
                eirs: ReponseCandidat::TYPE_E,
                raw: null
            ));
        }

        return $session;
    }


    protected function questionsTypeIndexAsValue(Test $test, array $indexes, string $type): void
    {
        foreach ($indexes as $index) {
            $test->questions->add(new QuestionTest(
                id: 0,
                indice: $index,
                intitule: "Q" . $index,
                abreviation: "Q" . $index,
                test: $test,
                type: $type
            ));
        }
    }

    protected function questionsTypeIndexAsKey(Test $test, array $index_to_any, string $type): void
    {
        foreach ($index_to_any as $index => $any) {
            $test->questions->add(new QuestionTest(
                id: 0,
                indice: $index,
                intitule: "Q" . $index,
                abreviation: "Q" . $index,
                test: $test,
                type: $type
            ));
        }
    }

    protected function echellesSimplesAptitudesCognitives(Structure $structure, array $nom_php_to_nom): void
    {
        foreach ($nom_php_to_nom as $nom_php => $nom) {
            $structure->echelles->add(new Echelle(
                id: 0,
                nom: $nom,
                nom_php: $nom_php,
                type: Echelle::TYPE_ECHELLE_SIMPLE,
                echelles_correcteur: new ArrayCollection(),
                echelles_etalonnage: new ArrayCollection(),
                structure: $structure
            ));
        }
    }

    protected function echellesSimplesEtCompositesPersonnalite(Structure $structure, array $nom_php_to_nom, array $nom_php_composite_to_noms_php_simples): void
    {
        foreach ($nom_php_composite_to_noms_php_simples as $nom_php_composite => $noms_php_simples) {

            $structure->echelles->add(new Echelle(
                id: 0,
                nom: $nom_php_to_nom[$nom_php_composite],
                nom_php: $nom_php_composite,
                type: Echelle::TYPE_ECHELLE_COMPOSITE,
                echelles_correcteur: new ArrayCollection(),
                echelles_etalonnage: new ArrayCollection(),
                structure: $structure
            ));

            foreach ($noms_php_simples as $nom_php_simple) {
                $structure->echelles->add(new Echelle(
                    id: 0,
                    nom: $nom_php_to_nom[$nom_php_simple],
                    nom_php: $nom_php_simple,
                    type: Echelle::TYPE_ECHELLE_SIMPLE,
                    echelles_correcteur: new ArrayCollection(),
                    echelles_etalonnage: new ArrayCollection(),
                    structure: $structure
                ));
            }
        }
    }

    protected function echellesCorrecteurPersonnalite(Structure $structure, Correcteur $correcteur, array $nom_php_composite_to_nom_php_simple_to_index_to_type): void
    {

        foreach ($nom_php_composite_to_nom_php_simple_to_index_to_type as $nom_php_composite => $nom_php_simple_to_index_to_type) {

            $expression_composite = "0";
            $echelle_composite = $this->findEchelleInProfil($structure, $nom_php_composite);

            foreach ($nom_php_simple_to_index_to_type as $nom_php_simple => $index_to_type) {

                $echelle_simple = $this->findEchelleInProfil($structure, $nom_php_simple);

                $expression_composite = $expression_composite . "+echelle(\"" . $nom_php_simple . "\")";

                $correcteur->echelles->add($this->echelleCorrecteur($correcteur, $echelle_simple, $index_to_type));
            }

            $correcteur->echelles->add(new EchelleCorrecteur(
                0,
                $expression_composite,
                $echelle_composite,
                $correcteur
            ));
        }
    }

    protected function echelleCorrecteur(Correcteur $correcteur, Echelle $echelle, array $index_to_function): EchelleCorrecteur
    {
        $expression = "0";
        foreach ($index_to_function as $index => $function) {
            $expression = $expression . "+" . $function . "(" . $index . ")";
        }

        return new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        );
    }

    protected function echellesCorrecteurAptitudeCognitive(Structure $structure, Correcteur $correcteur, array $nom_php_to_index_to_vrai, $base): void
    {
        foreach ($nom_php_to_index_to_vrai as $nom_php => $index_to_vrai) {
            $echelle = $this->findEchelleInProfil($structure, $nom_php);
            $expression = "0";

            foreach ($index_to_vrai as $index => $vrai) {
                $expression = $expression . "+" . $base . $vrai . "(" . $index . ")";
            }

            $correcteur->echelles->add(new EchelleCorrecteur(
                0,
                $expression,
                $echelle,
                $correcteur
            ));
        }
    }
    protected function findEchelleInProfil(Structure $structure, string $nom_php): Echelle|null
    {
        /** @var Echelle $echelle */
        foreach ($structure->echelles as $echelle) {
            if ($echelle->nom_php === $nom_php) {
                return $echelle;
            }
        }
        return null;
    }

    protected abstract function questions(Test $test);

    protected abstract function aptitudesCognitives(Structure $structure);

    protected abstract function personnalite(Structure $structure);

    protected abstract function correcteurAptitudesCognitives(Structure $structure, Correcteur $correcteur);

    protected abstract function correcteurPersonnalite(Structure $structure, Correcteur $correcteur);
}



