<?php

namespace App\Fixture\Bmpr;

use App\Core\Renderer\RendererRepository;
use App\Core\Renderer\Values\RendererBatonnets;
use App\Entity\Echelle;
use App\Entity\EchelleGraphique;
use App\Entity\Graphique;
use App\Entity\Profil;
use App\Entity\Subtest;
use App\Repository\ProfilRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BpmrOffGraphiqueFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function __construct(
        private readonly ProfilRepository $profil_repository,
    )
    {
    }

    public static function getGroups(): array
    {
        return ["bpmr"];
    }

    public function getDependencies(): array
    {
        return [BpmrOffFixture::class];
    }

    public function load(ObjectManager $manager)
    {
        $profil = $this->profil_repository->findOneBy(["nom" => BpmrOffFixture::PROFIL_NOM]);


        // ---------- Graphique ----------

        $renderer_index = RendererRepository::INDEX_BATONNETS;
        $renderer = new RendererBatonnets();
        $graphique = new Graphique(
            id: 0,
            options: $renderer->initializeOptions(),
            profil: $profil,
            echelles: new ArrayCollection(),
            nom: "Graphique par défaut",
            renderer_index: $renderer_index,
            subtests: new ArrayCollection()
        );
        $this->echellesGraphiques($profil, $graphique, $renderer);
        $manager->persist($graphique);
        $manager->flush();

        $graphique->subtests->add($this->subtestAptitudesCognitives($graphique));
        $graphique->subtests->add($this->subtestPersonnalite($graphique));
        $manager->flush();
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

    private function subtestAptitudesCognitives(Graphique $graphique): Subtest
    {
        $echelles_bas_de_cadre = array(
            array($this->findEchelleInGraphique($graphique, BpmrOffFixture::EG)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
            array($this->findEchelleInGraphique($graphique, BpmrOffFixture::QR)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
        );

        $echelles_core = [];

        foreach (BpmrOffFixture::APTITUDES_COGNITIVES_BR_TO_MR as $br => $mr) {
            $echelle_br = $this->findEchelleInGraphique($graphique, $br);
            $echelle_mr = $this->findEchelleInGraphique($graphique, $mr);
            $echelles_core[] = array($echelle_br->id, $echelle_mr->id);
        }

        return new Subtest(
            id: 0,
            nom: "Aptitudes cognitives",
            type: Subtest::TYPE_SUBTEST_BR_MR,
            echelles_core: $echelles_core,
            echelles_bas_de_cadre: $echelles_bas_de_cadre,
            graphique: $graphique
        );
    }

    private function subtestPersonnalite(Graphique $graphique): Subtest
    {
        $echelles_bas_de_cadre = array(
            array($this->findEchelleInGraphique($graphique, BpmrOffFixture::DS)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
            array($this->findEchelleInGraphique($graphique, BpmrOffFixture::AT)->id, Subtest::TYPE_FOOTER_SCORE_ONLY),
            array($this->findEchelleInGraphique($graphique, BpmrOffFixture::RC)->id, Subtest::TYPE_FOOTER_SCORE_ONLY),
        );

        $echelles_core = array();

        foreach (BpmrOffFixture::NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE as $nom_php_composite => $noms_php_simples) {

            $echelle_composite_dependencies = array();
            $echelle_composite = $this->findEchelleInGraphique($graphique, $nom_php_composite);

            foreach ($noms_php_simples as $nom_php_simple) {
                $echelle_simple = $this->findEchelleInGraphique($graphique, $nom_php_simple);
                $echelle_composite_dependencies[] = $echelle_simple->id;
            }

            $echelles_core[] = array($echelle_composite->id, $echelle_composite_dependencies);
        }

        return new Subtest(
            id: 0,
            nom: "Personnalité",
            type: Subtest::TYPE_SUBTEST_COMPOSITE,
            echelles_core: $echelles_core,
            echelles_bas_de_cadre: $echelles_bas_de_cadre,
            graphique: $graphique
        );
    }

    private function findEchelleInGraphique(Graphique $graphique, string $nom_php): EchelleGraphique|null
    {
        /** @var EchelleGraphique $echelle */
        foreach ($graphique->echelles as $echelle) {
            if ($echelle->echelle->nom_php === $nom_php) {
                return $echelle;
            }
        }
        return null;
    }
}



