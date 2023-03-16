<?php

namespace App\Fixture\Bmpr;

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
use App\Entity\Subtest;
use App\Fixture\InitFixture;
use App\Repository\ConcoursRepository;
use App\Repository\GrilleRepository;
use App\Repository\ProfilRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BpmrOffEtalonnageFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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

        $nombre_classes = 9;

        $etalonnage = new Etalonnage(
            0,
            $profil,
            self::ETALONNAGE_NOM,
            $nombre_classes,
            new ArrayCollection()
        );

        foreach ($profil->echelles as $echelle) {
            $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle($echelle, $etalonnage, $nombre_classes));
        }

        $manager->persist($etalonnage);
        $manager->flush();
    }

    const ETALONNAGE_NOM = "Etalonnage BMPR OFF test";
}



