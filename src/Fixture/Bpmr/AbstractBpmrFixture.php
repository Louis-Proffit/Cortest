<?php

namespace App\Fixture\Bpmr;

use App\Core\Renderer\RendererRepository;
use App\Core\Renderer\Values\RendererBatonnets;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\EchelleGraphique;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\Profil;
use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
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
        private readonly string $profil_nom,
        private readonly string $correcteur_nom,
        private readonly string $etalonnage_nom,
        private readonly int    $etalonnage_nombre_classes,
        private readonly string $graphique_nom,
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

    public function load(ObjectManager $manager)
    {
        // Session concours profil --------------------------------
        $concours = new Concours(
            0,
            $this->concours_nom,
            new ArrayCollection(),
            new ArrayCollection(),
            GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            $this->type_concours,
            $this->version_batterie,
            new ArrayCollection()
        );

        $session = $this->sessionExemple(
            $concours,
            $manager->getRepository(Sgap::class)->findOneBy([]),
            $manager->getRepository(NiveauScolaire::class)->findOneBy([]),
        );

        $this->questions($concours);

        $profil = new Profil(
            0,
            $this->profil_nom,
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection()
        );

        $this->aptitudesCognitives($profil);
        $this->personnalite($profil);

        $manager->persist($concours);
        $manager->persist($profil);
        $manager->persist($session);
        $manager->flush();

        // CORRECTEUR -----------------------
        $correcteur = new Correcteur(
            0,
            $concours,
            $profil,
            $this->correcteur_nom,
            new ArrayCollection()
        );

        $manager->persist($correcteur);
        $this->correcteurPersonnalite($profil, $correcteur);
        $this->correcteurAptitudesCognitives($profil, $correcteur);

        // ETALONNAGE --------------------------

        $etalonnage = new Etalonnage(
            0,
            $profil,
            $this->etalonnage_nom,
            $this->etalonnage_nombre_classes,
            new ArrayCollection()
        );

        foreach ($profil->echelles as $echelle) {
            $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle($echelle,
                $etalonnage,
                $this->etalonnage_nombre_classes));
        }

        $manager->persist($etalonnage);

        // GRAPHIQUE -------------------------------
        $renderer_index = RendererRepository::INDEX_BATONNETS;
        $renderer = new RendererBatonnets();
        $graphique = new Graphique(
            id: 0,
            options: $renderer->initializeOptions(),
            profil: $profil,
            echelles: new ArrayCollection(),
            nom: $this->graphique_nom,
            renderer_index: $renderer_index,
            subtests: new ArrayCollection()
        );
        $this->echellesGraphiques($profil, $graphique, $renderer);
        $manager->persist($graphique);
        $manager->flush();

        $this->subtests($graphique);

        $manager->flush();
    }

    private function sessionExemple(Concours $concours, Sgap $sgap, NiveauScolaire $niveau_scolaire): Session
    {
        $session = new Session(
            0,
            new DateTime("now"),
            0,
            "Pas d'observations",
            $concours,
            $sgap,
            new ArrayCollection()
        );

        $reponses = array_fill(1, $this->nombre_questions, 3);

        foreach (range(1, 20) as $index) {
            $session->reponses_candidats->add(new ReponseCandidat(
                0,
                $session,
                $reponses,
                "NOM " . $index,
                "PRENOM " . $index,
                "Sans objet",
                $niveau_scolaire,
                new DateTime("now"),
                ReponseCandidat::INDEX_HOMME,
                "RESERVE TEST",
                "AUTRE 1",
                "AUTRE 2",
                1,
                null
            ));
        }

        return $session;
    }


    protected function questionsTypeIndexAsValue(Concours $concours, array $indexes, string $type)
    {
        foreach ($indexes as $index => $any) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                $type
            ));
        }
    }

    protected function questionsTypeIndexAsKey(Concours $concours, array $index_to_any, string $type)
    {
        foreach ($index_to_any as $index => $any) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                $type
            ));
        }
    }

    protected function echellesSimplesAptitudesCognitives(Profil $profil, array $nom_php_to_nom)
    {
        foreach ($nom_php_to_nom as $nom_php => $nom) {
            $profil->echelles->add(new Echelle(
                id: 0,
                nom: $nom,
                nom_php: $nom_php,
                type: Echelle::TYPE_ECHELLE_SIMPLE,
                echelles_correcteur: new ArrayCollection(),
                echelles_etalonnage: new ArrayCollection()
            ));
        }
    }

    protected function echellesSimplesEtCompositesPersonnalite(Profil $profil, array $nom_php_to_nom, array $nom_php_composite_to_noms_php_simples)
    {
        foreach ($nom_php_composite_to_noms_php_simples as $nom_php_composite => $noms_php_simples) {

            $profil->echelles->add(new Echelle(
                id: 0,
                nom: $nom_php_to_nom[$nom_php_composite],
                nom_php: $nom_php_composite,
                type: Echelle::TYPE_ECHELLE_COMPOSITE,
                echelles_correcteur: new ArrayCollection(),
                echelles_etalonnage: new ArrayCollection()
            ));

            foreach ($noms_php_simples as $nom_php_simple) {
                $profil->echelles->add(new Echelle(
                    id: 0,
                    nom: $nom_php_to_nom[$nom_php_simple],
                    nom_php: $nom_php_simple,
                    type: Echelle::TYPE_ECHELLE_SIMPLE,
                    echelles_correcteur: new ArrayCollection(),
                    echelles_etalonnage: new ArrayCollection()
                ));
            }
        }
    }

    protected function echellesCorrecteurPersonnalite(Profil $profil, Correcteur $correcteur, array $nom_php_composite_to_nom_php_simple_to_index_to_type)
    {

        foreach ($nom_php_composite_to_nom_php_simple_to_index_to_type as $nom_php_composite => $nom_php_simple_to_index_to_type) {

            $expression_composite = "0";
            $echelle_composite = $this->findEchelleInProfil($profil, $nom_php_composite);

            foreach ($nom_php_simple_to_index_to_type as $nom_php_simple => $index_to_type) {

                $echelle_simple = $this->findEchelleInProfil($profil, $nom_php_simple);

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

    protected function echellesCorrecteurAptitudeCognitive(Profil $profil, Correcteur $correcteur, array $nom_php_to_index_to_vrai, $base)
    {
        foreach ($nom_php_to_index_to_vrai as $nom_php => $index_to_vrai) {
            $echelle = $this->findEchelleInProfil($profil, $nom_php);
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

    private function echellesGraphiques(Profil $profil, Graphique $graphique, RendererBatonnets $renderer)
    {
        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            $graphique->echelles->add(new EchelleGraphique(
                id: 0,
                options: $renderer->initializeEchelleOption($echelle),
                echelle: $echelle,
                graphique: $graphique
            ));
        }
    }

    protected function findEchelleInProfil(Profil $profil, string $nom_php): Echelle|null
    {
        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            if ($echelle->nom_php === $nom_php) {
                return $echelle;
            }
        }
        return null;
    }

    protected function findEchelleInGraphique(Graphique $graphique, string $nom_php): EchelleGraphique|null
    {
        /** @var EchelleGraphique $echelle */
        foreach ($graphique->echelles as $echelle) {
            if ($echelle->echelle->nom_php === $nom_php) {
                return $echelle;
            }
        }
        return null;
    }

    protected abstract function questions(Concours $concours);

    protected abstract function aptitudesCognitives(Profil $profil);

    protected abstract function personnalite(Profil $profil);

    protected abstract function correcteurAptitudesCognitives(Profil $profil, Correcteur $correcteur);

    protected abstract function correcteurPersonnalite(Profil $profil, Correcteur $correcteur);

    protected abstract function subtests(Graphique $graphique);
}



