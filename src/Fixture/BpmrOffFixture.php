<?php

namespace App\Fixture;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\NiveauScolaire;
use App\Entity\Profil;
use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Repository\GrilleRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BpmrOffFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $concours = $this->concours();

        $this->questions($concours);

        $profil = new Profil(
            0,
            "BPMR - Officier",
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection()
        );

        // -------------------- correcteurs

        $correcteur = new Correcteur(
            0,
            $concours,
            $profil,
            "Correcteur par défaut",
            new ArrayCollection()
        );

        $this->vt_br($profil, $correcteur);
        $this->vt_mr($profil, $correcteur);
        $this->anx($profil, $correcteur);
        $this->fp($profil, $correcteur);

        $manager->persist($concours);
        $manager->persist($profil);
        $manager->persist($correcteur);

        $session = $this->session_exemple(
            $concours,
            $manager->getRepository(Sgap::class)->findOneBy([]),
            $manager->getRepository(NiveauScolaire::class)->findOneBy([]),
        );

        $manager->persist($session);

        $manager->flush();
    }

    private function session_exemple(Concours $concours, Sgap $sgap, NiveauScolaire $niveau_scolaire): Session
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

        $reponses = array_fill(1, 456, 2);

        $session->reponses_candidats->add(new ReponseCandidat(
            0,
            $session,
            $reponses,
            "NOM TEST",
            "PRENOM TEST",
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

        return $session;
    }

    private function questions(Concours $concours)
    {
        $concours->questions->add(new QuestionConcours(
            0,
            1,
            $concours,
            QuestionConcours::TYPE_EXEMPLE
        ));

        for ($i = 1; $i <= 51; $i++) {
            $concours->questions->add(new QuestionConcours(
                0,
                $i,
                $concours,
                QuestionConcours::TYPE_VRAI_FAUX
            ));
        }
    }

    private function vt_br(Profil $profil, Correcteur $correcteur)
    {
        $expression = "0";

        foreach (self::REPONSES_VT_BR as $value) {
            $expression = $expression . "+" . $value;
        }

        $this->addEchelle($profil, $correcteur, "VT_BR", Echelle::TYPE_ECHELLE_SIMPLE, "vt_br", $expression);
    }

    private function vt_mr(Profil $profil, Correcteur $correcteur)
    {
        $expression = "0";

        foreach (self::REPONSES_VT_MR as $value) {
            $expression = $expression . "+" . $value;
        }

        $this->addEchelle($profil, $correcteur, "VT_MR", Echelle::TYPE_ECHELLE_SIMPLE, "vt_mr", $expression);
    }

    private function anx(Profil $profil, Correcteur $correcteur)
    {
        $expression = "score43210(200)+score43210(226)+score43210(251)+score43210(277)+score43210(303)+score43210(329)+score43210(355)+score43210(381)+score43210(407)+score43210(433)";

        $this->addEchelle($profil, $correcteur, "anx", Echelle::TYPE_ECHELLE_SIMPLE, "anx", $expression);
    }

    private function fp(Profil $profil, Correcteur $correcteur)
    {
        $expression = "echelle(\"anx\")+0";

        $this->addEchelle($profil, $correcteur, "fp", Echelle::TYPE_ECHELLE_COMPOSITE, "fp", $expression);
    }

    private function addEchelle(Profil $profil, Correcteur $correcteur, string $nom, string $type, string $nom_php, string $expression)
    {
        $echelle = new Echelle(0,
            $nom,
            $nom_php,
            $type,
            new ArrayCollection(),
            new ArrayCollection());

        $profil->echelles->add($echelle);

        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur,
        ));
    }

    private function concours(): Concours
    {
        return new Concours(
            0,
            "Concours BPMR - Officier",
            new ArrayCollection(),
            new ArrayCollection(),
            GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            "[Type concours]",
            "[Version batterie]",
            new ArrayCollection()
        );
    }

    public static function getGroups(): array
    {
        return ["bpmr"];
    }

    public function getDependencies(): array
    {
        return [InitFixture::class];
    }


    const REPONSES_VT_BR = [
        2 => "vraiB(2)",
        3 => "vraiE(3)",
        4 => "vraiA(4)",
        5 => "vraiD(5)",
        6 => "vraiE(6)",
        7 => "vraiD(7)",
        8 => "vraiC(8)",
        9 => "vraiB(9)",
        10 => "vraiC(10)",
        11 => "vraiE(11)",
        12 => "vraiE(12)",
        13 => "vraiB(13)",
        14 => "vraiD(14)",
        15 => "vraiB(15)",
        16 => "vraiB(16)",
        17 => "vraiD(17)",
        18 => "vraiC(18)",
        19 => "vraiD(19)",
        20 => "vraiD(20)",
        21 => "vraiC(21)",
        22 => "vraiC(22)",
        23 => "vraiB(23)",
        24 => "vraiC(24)",
        25 => "vraiB(25)",
        26 => "vraiA(26)",
        27 => "vraiA(27)",
        28 => "vraiA(28)",
        29 => "vraiD(29)",
        30 => "vraiA(30)",
        31 => "vraiB(31)",
        32 => "vraiA(32)",
        33 => "vraiC(33)",
        34 => "vraiC(34)",
        35 => "vraiA(35)",
        36 => "vraiA(36)",
        37 => "vraiB(37)",
        38 => "vraiB(38)",
        39 => "vraiB(39)",
        40 => "vraiC(40)",
        41 => "vraiC(41)",
        42 => "vraiD(42)",
        43 => "vraiE(43)",
        44 => "vraiB(44)",
        45 => "vraiA(45)",
        46 => "vraiC(46)",
        47 => "vraiA(47)",
        48 => "vraiB(48)",
        49 => "vraiE(49)",
        50 => "vraiB(50)",
        51 => "vraiE(51)"];

    const REPONSES_VT_MR = [
        2 => "fauxB(2)",
        3 => "fauxE(3)",
        4 => "fauxA(4)",
        5 => "fauxD(5)",
        6 => "fauxE(6)",
        7 => "fauxD(7)",
        8 => "fauxC(8)",
        9 => "fauxB(9)",
        10 => "fauxC(10)",
        11 => "fauxE(11)",
        12 => "fauxE(12)",
        13 => "fauxB(13)",
        14 => "fauxD(14)",
        15 => "fauxB(15)",
        16 => "fauxB(16)",
        17 => "fauxD(17)",
        18 => "fauxC(18)",
        19 => "fauxD(19)",
        20 => "fauxD(20)",
        21 => "fauxC(21)",
        22 => "fauxC(22)",
        23 => "fauxB(23)",
        24 => "fauxC(24)",
        25 => "fauxB(25)",
        26 => "fauxA(26)",
        27 => "fauxA(27)",
        28 => "fauxA(28)",
        29 => "fauxD(29)",
        30 => "fauxA(30)",
        31 => "fauxB(31)",
        32 => "fauxA(32)",
        33 => "fauxC(33)",
        34 => "fauxC(34)",
        35 => "fauxA(35)",
        36 => "fauxA(36)",
        37 => "fauxB(37)",
        38 => "fauxB(38)",
        39 => "fauxB(39)",
        40 => "fauxC(40)",
        41 => "fauxC(41)",
        42 => "fauxD(42)",
        43 => "fauxE(43)",
        44 => "fauxB(44)",
        45 => "fauxA(45)",
        46 => "fauxC(46)",
        47 => "fauxA(47)",
        48 => "fauxB(48)",
        49 => "fauxE(49)",
        50 => "fauxB(50)",
        51 => "fauxE(51)"];
}



