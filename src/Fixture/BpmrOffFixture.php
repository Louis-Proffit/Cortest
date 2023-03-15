<?php

namespace App\Fixture;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
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

        $this->vt($profil, $correcteur);
        $this->sp($profil, $correcteur);
        $this->rais($profil, $correcteur);
        $this->cv($profil, $correcteur);
        $this->sl($profil, $correcteur);
        $this->dic($profil, $correcteur);
        $this->as($profil, $correcteur);
        $this->eg($profil, $correcteur);
        $this->qr($profil, $correcteur);

        $this->at($profil, $correcteur);
        $this->ds($profil, $correcteur);
        $this->fp($profil, $correcteur);
        $this->me($profil, $correcteur);
        $this->cp($profil, $correcteur);
        $this->ar($profil, $correcteur);
        $this->pm($profil, $correcteur);
        $this->rc($profil, $correcteur);

        $etalonnage = $this->etalonnage($profil);

        $manager->persist($concours);
        $manager->persist($profil);
        $manager->persist($correcteur);
        $manager->persist($etalonnage);


        $session = $this->session_exemple(
            $concours,
            $manager->getRepository(Sgap::class)->findOneBy([]),
            $manager->getRepository(NiveauScolaire::class)->findOneBy([]),
        );

        $manager->persist($session);

        $manager->flush();
    }

    private function etalonnage(Profil $profil): Etalonnage
    {
        $nombre_classes = 9;

        $echelles = new ArrayCollection();

        $etalonnage = new Etalonnage(
            0,
            $profil,
            "Etalonnage BMPR OFF test",
            $nombre_classes,
            $echelles
        );

        foreach ($profil->echelles as $echelle) {
            $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle($echelle, $etalonnage, $nombre_classes));
        }

        return $etalonnage;
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

        $reponses = array_fill(1, 456, 3);

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
        foreach (self::INDEX_EXEMPLES as $index_exemple) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index_exemple,
                $concours,
                QuestionConcours::TYPE_EXEMPLE
            ));
        }

        foreach ((
            self::REPONSES_VT_INDEX_TO_VRAI +
            self::REPONSES_AS_INDEX_TO_VRAI +
            self::REPONSES_DIC_INDEX_TO_VRAI +
            self::REPONSES_SL_INDEX_TO_VRAI +
            self::REPONSES_CV_INDEX_TO_VRAI +
            self::REPONSES_RAIS_INDEX_TO_VRAI +
            self::REPONSES_SP_INDEX_TO_VRAI
        ) as $index => $vrai) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                QuestionConcours::TYPE_VRAI_FAUX
            ));
        }

        foreach (self::ALL_PERSONNALITE as $index => $type
        ) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                QuestionConcours::TYPE_SCORE
            ));
        }

    }

    private function vt(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "VT bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::VT_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_VT_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "VT mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::VT_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_VT_INDEX_TO_VRAI, "faux"));
    }

    private function sp(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "SP bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::SP_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_SP_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "SP mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::SP_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_SP_INDEX_TO_VRAI, "faux"));
    }

    private function rais(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "Rais bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::RAIS_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_RAIS_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "Rais mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::RAIS_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_RAIS_INDEX_TO_VRAI, "faux"));
    }

    private function cv(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "CV bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::CV_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_CV_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "CV mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::CV_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_CV_INDEX_TO_VRAI, "faux"));
    }

    private function sl(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "SL bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::SL_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_SL_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "SL mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::SL_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_SL_INDEX_TO_VRAI, "faux"));
    }

    private function dic(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "DIC bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::DIC_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_DIC_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "DIC mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::DIC_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_DIC_INDEX_TO_VRAI, "faux"));
    }

    private function as(Profil $profil, Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "AS bonnes réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::AS_BR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_AS_INDEX_TO_VRAI, "vrai"));
        $this->addEchelle(
            $profil,
            $correcteur,
            "AS mauvaises réponses",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::AS_MR,
            $this->expressionFromArrayAptitudeCognitive(self::REPONSES_AS_INDEX_TO_VRAI, "faux"));
    }

    private function eg(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $expression = "((" . $this->nombreBonnesReponsesCognitif() . ")*(" . $this->nombreReponsesTraiteesCognitif() . ")) ** 0.5";

        $this->addEchelle(
            $profil,
            $correcteur,
            "EG",
            Echelle::TYPE_ECHELLE_COMPOSITE,
            self::EG,
            $expression
        );
    }

    private function qr(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $expression = "((" . $this->nombreBonnesReponsesCognitif() . ")/(" . $this->nombreReponsesTraiteesCognitif() . ")) * 100";

        $this->addEchelle(
            $profil,
            $correcteur,
            "QR",
            Echelle::TYPE_ECHELLE_COMPOSITE,
            self::QR,
            $expression
        );
    }

    private function nombreReponsesTraiteesCognitif(): string
    {
        $nombre_reponses_traitees = "0";
        foreach ((
            self::REPONSES_VT_INDEX_TO_VRAI +
            self::REPONSES_SP_INDEX_TO_VRAI +
            self::REPONSES_RAIS_INDEX_TO_VRAI +
            self::REPONSES_CV_INDEX_TO_VRAI +
            self::REPONSES_SL_INDEX_TO_VRAI +
            self::REPONSES_DIC_INDEX_TO_VRAI +
            self::REPONSES_AS_INDEX_TO_VRAI
        ) as $index => $vrai) {
            $nombre_reponses_traitees = $nombre_reponses_traitees . "+repondu(" . $index . ")";
        }

        return $nombre_reponses_traitees;
    }

    private function nombreBonnesReponsesCognitif(): string
    {
        $nombre_bonnes_reponses = "0";

        foreach ([self::VT_BR, self::SP_BR, self::RAIS_BR, self::CV_BR, self::SL_BR, self::DIC_BR, self::AS_BR] as $echelle) {
            $nombre_bonnes_reponses = $nombre_bonnes_reponses . "+echelle(\"" . $echelle . "\")";
        }

        return $nombre_bonnes_reponses;
    }

    private function expressionFromArrayAptitudeCognitive(array $index_to_vrai, string $base): string
    {
        $expression = "0";

        foreach ($index_to_vrai as $index => $vrai) {
            $expression = $expression . "+" . $base . $vrai . "(" . $index . ")";
        }

        return $expression;
    }

    private function at(
        Profil     $profil,
        Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "AT",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::AT,
            $this->expressionFromArraySimplePersonalite(self::AT_REPONSES)
        );
    }

    private function ds(
        Profil     $profil,
        Correcteur $correcteur)
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            "DS",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::DS,
            $this->expressionFromArraySimplePersonalite(self::DS_REPONSES)
        );
    }

    private function fp(
        Profil     $profil,
        Correcteur $correcteur,
    )
    {
        $this->echelleSimplePersonalite($profil, $correcteur, "FP1 : Anx", self::FP_ANX, self::FP_ANX_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "FP2 : Instros",
            self::FP_INSTROS,
            self::FP_INSTROS_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "FP3 : Hdep", self::FP_HDEP, self::FP_HDEP_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "FP4 : Dev", self::FP_DEV, self::FP_DEV_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "FP5 : Gen", self::FP_GEN, self::FP_GEN_REPONSES);
        $this->echelleCompositePersonalite($profil,
            $correcteur,
            "FP",
            self::FP,
            [self::FP_ANX, self::FP_INSTROS, self::FP_HDEP, self::FP_DEV, self::FP_GEN]);
    }

    private function me(
        Profil     $profil,
        Correcteur $correcteur,
    )
    {
        $this->echelleSimplePersonalite($profil, $correcteur, "ME1 : CE", self::ME_CE, self::ME_CE_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "ME2 : Modes",
            self::ME_MODES,
            self::ME_MODES_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "ME3 : Coope", self::ME_COOPE, self::ME_COOPE_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "ME4 : Amb", self::ME_AMB, self::ME_AMB_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "ME5 : Droit", self::ME_DROIT, self::ME_DROIT_REPONSES);
        $this->echelleCompositePersonalite($profil,
            $correcteur,
            "ME",
            self::ME,
            [self::ME_CE, self::ME_MODES, self::ME_COOPE, self::ME_AMB, self::ME_DROIT]);
    }

    private function cp(
        Profil     $profil,
        Correcteur $correcteur,
    )
    {
        $this->echelleSimplePersonalite($profil, $correcteur, "CP1 : Fiab", self::CP_FIAB, self::CP_FIAB_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "CP2 : Autodisc",
            self::CP_AUTODISC,
            self::CP_AUTODISC_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "CP3 : Refl", self::CP_REFL, self::CP_REFL_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "CP4 : Rig", self::CP_RIG, self::CP_RIG_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "CP5 : Sva", self::CP_SVA, self::CP_SVA_REPONSES);
        $this->echelleCompositePersonalite($profil,
            $correcteur,
            "CP",
            self::CP,
            [self::CP_FIAB, self::CP_AUTODISC, self::CP_REFL, self::CP_RIG, self::CP_SVA]);
    }

    private function ar(
        Profil     $profil,
        Correcteur $correcteur,
    )
    {
        $this->echelleSimplePersonalite($profil, $correcteur, "AR1 : Conf", self::AR_CONF, self::AR_CONF_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "AR2 : Grega",
            self::AR_GREGA,
            self::AR_GREGA_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "AR3 : Spont", self::AR_SPONT, self::AR_SPONT_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "AR4 : Nouv", self::AR_NOUV, self::AR_NOUV_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "AR5 : Dyn", self::AR_DYN, self::AR_DYN_REPONSES);
        $this->echelleCompositePersonalite($profil,
            $correcteur,
            "AR",
            self::AR,
            [self::AR_CONF, self::AR_GREGA, self::AR_SPONT, self::AR_NOUV, self::AR_DYN]);
    }

    private function pm(
        Profil     $profil,
        Correcteur $correcteur,
    )
    {
        $this->echelleSimplePersonalite($profil, $correcteur, "PM1", self::PM_1, self::PM_1_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "PM2 : Affirm",
            self::PM_AFFIRM,
            self::PM_AFFIRM_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "PM3 : Emp", self::PM_EMP, self::PM_EMP_REPONSES);
        $this->echelleSimplePersonalite($profil, $correcteur, "PM4 : Ing", self::PM_ING, self::PM_ING_REPONSES);
        $this->echelleSimplePersonalite($profil,
            $correcteur,
            "PM5 : S. Intel",
            self::PM_SINTEL,
            self::PM_SINTEL_REPONSES);
        $this->echelleCompositePersonalite($profil,
            $correcteur,
            "PM",
            self::PM,
            [self::PM_1, self::PM_AFFIRM, self::PM_EMP, self::PM_ING, self::PM_SINTEL]);
    }

    private function rc(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $expression = "0";
        foreach (self::ALL_PERSONNALITE as $index => $type
        ) {
            $expression = $expression . "+vraiC(" . $index . ")";
        }

        $this->addEchelle(
            $profil,
            $correcteur,
            "rc",
            Echelle::TYPE_ECHELLE_SIMPLE,
            self::RC,
            $expression
        );
    }

    private function echelleSimplePersonalite(
        Profil     $profil,
        Correcteur $correcteur,
        string     $nom,
        string     $nom_php,
        array      $reponses
    )
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            $nom,
            Echelle::TYPE_ECHELLE_SIMPLE,
            $nom_php,
            $this->expressionFromArraySimplePersonalite($reponses)
        );
    }

    private function echelleCompositePersonalite(
        Profil     $profil,
        Correcteur $correcteur,
        string     $nom,
        string     $nom_php,
        array      $echelles
    )
    {
        $this->addEchelle(
            $profil,
            $correcteur,
            $nom,
            Echelle::TYPE_ECHELLE_COMPOSITE,
            $nom_php,
            $this->expressionFromArrayCompositePersonalite($echelles)
        );
    }

    private function expressionFromArraySimplePersonalite(array $index_to_nom): string
    {
        $expression = "0";

        foreach ($index_to_nom as $index => $nom) {
            $expression = $expression . "+" . $nom . "(" . $index . ")";
        }

        return $expression;
    }

    private function expressionFromArrayCompositePersonalite(array $echelles): string
    {
        $expression = "0";

        foreach ($echelles as $echelle) {
            $expression = $expression . "+echelle(\"" . $echelle . "\")";
        }

        return $expression;
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

    const INDEX_EXEMPLES = [1, 52, 53, 74, 75, 76, 92, 113, 129, 150];

    const A = "A";
    const B = "B";
    const C = "C";
    const D = "D";
    const E = "E";

    const EG = "eg";
    const QR = "qr";

    const VT_BR = "vt_br";
    const VT_MR = "vt_mr";
    const REPONSES_VT_INDEX_TO_VRAI = [
        2 => self::B,
        3 => self::E,
        4 => self::A,
        5 => self::D,
        6 => self::E,
        7 => self::D,
        8 => self::C,
        9 => self::B,
        10 => self::C,
        11 => self::E,
        12 => self::E,
        13 => self::B,
        14 => self::D,
        15 => self::B,
        16 => self::B,
        17 => self::D,
        18 => self::C,
        19 => self::D,
        20 => self::D,
        21 => self::C,
        22 => self::C,
        23 => self::B,
        24 => self::C,
        25 => self::B,
        26 => self::A,
        27 => self::A,
        28 => self::A,
        29 => self::D,
        30 => self::A,
        31 => self::B,
        32 => self::A,
        33 => self::C,
        34 => self::C,
        35 => self::A,
        36 => self::A,
        37 => self::B,
        38 => self::B,
        39 => self::B,
        40 => self::C,
        41 => self::C,
        42 => self::D,
        43 => self::E,
        44 => self::B,
        45 => self::A,
        46 => self::C,
        47 => self::A,
        48 => self::B,
        49 => self::E,
        50 => self::B,
        51 => self::E,

    ];

    const SP_BR = "sp_br";
    const SP_MR = "sp_mr";
    const REPONSES_SP_INDEX_TO_VRAI = [
        54 => self::B,
        55 => self::D,
        56 => self::D,
        57 => self::C,
        58 => self::D,
        59 => self::B,
        60 => self::B,
        61 => self::B,
        62 => self::C,
        63 => self::E,
        64 => self::D,
        65 => self::E,
        66 => self::C,
        67 => self::E,
        68 => self::D,
        69 => self::E,
        70 => self::B,
        71 => self::E,
        72 => self::D,
        73 => self::C,

    ];

    const RAIS_BR = "rais_br";
    const RAIS_MR = "rais_mr";
    const REPONSES_RAIS_INDEX_TO_VRAI = [
        77 => self::D,
        78 => self::C,
        79 => self::C,
        80 => self::B,
        81 => self::C,
        82 => self::B,
        83 => self::B,
        84 => self::D,
        85 => self::A,
        86 => self::C,
        87 => self::C,
        88 => self::A,
        89 => self::D,
        90 => self::B,
        91 => self::C,
    ];


    const CV_BR = "cv_br";
    const CV_MR = "cv_mr";
    const REPONSES_CV_INDEX_TO_VRAI = [
        93 => self::B,
        94 => self::B,
        95 => self::D,
        96 => self::B,
        97 => self::E,
        98 => self::A,
        99 => self::E,
        100 => self::B,
        101 => self::B,
        102 => self::B,
        103 => self::E,
        104 => self::A,
        105 => self::D,
        106 => self::D,
        107 => self::C,
        108 => self::C,
        109 => self::A,
        110 => self::B,
        111 => self::B,
        112 => self::B,
    ];


    const SL_BR = "sl_br";
    const SL_MR = "sl_mr";
    const REPONSES_SL_INDEX_TO_VRAI = [
        114 => self::B,
        115 => self::D,
        116 => self::A,
        117 => self::D,
        118 => self::A,
        119 => self::C,
        120 => self::D,
        121 => self::B,
        122 => self::D,
        123 => self::A,
        124 => self::C,
        125 => self::A,
        126 => self::D,
        127 => self::C,
        128 => self::B,
    ];

    const DIC_BR = "dic_br";
    const DIC_MR = "dic_mr";
    const REPONSES_DIC_INDEX_TO_VRAI = [
        130 => self::D,
        131 => self::D,
        132 => self::E,
        133 => self::B,
        134 => self::C,
        135 => self::A,
        136 => self::C,
        137 => self::C,
        138 => self::C,
        139 => self::A,
        140 => self::E,
        141 => self::D,
        142 => self::E,
        143 => self::E,
        144 => self::B,
        145 => self::E,
        146 => self::E,
        147 => self::D,
        148 => self::D,
        149 => self::D,

    ];

    const AS_BR = "as_br";
    const AS_MR = "as_mr";
    const REPONSES_AS_INDEX_TO_VRAI = [
        151 => self::A,
        152 => self::A,
        153 => self::B,
        154 => self::A,
        155 => self::A,
        156 => self::B,
        157 => self::A,
        158 => self::B,
        159 => self::B,
        160 => self::B,
        161 => self::A,
        162 => self::A,
        163 => self::A,
        164 => self::B,
        165 => self::B,
        166 => self::B,
        167 => self::A,
        168 => self::A,
        169 => self::B,
        170 => self::A,
        171 => self::B,
        172 => self::A,
        173 => self::B,
        174 => self::B,
        175 => self::B,
        176 => self::B,
        177 => self::A,
        178 => self::A,
        179 => self::B,
        180 => self::A,
        181 => self::B,
        182 => self::B,
        183 => self::B,
        184 => self::A,
        185 => self::A,
        186 => self::B,
        187 => self::B,
        188 => self::B,
        189 => self::A,
        190 => self::B,
        191 => self::B,
        192 => self::A,
        193 => self::B,
        194 => self::B,
        195 => self::B,
    ];


    const NORMAL = "score43210";
    const INVERSE = "score01234";

    const AT = "at";
    const AT_REPONSES = [
        196 => self::NORMAL,
        340 => self::NORMAL,
        456 => self::NORMAL
    ];

    const DS = "ds";
    const DS_REPONSES = [
        361 => self::NORMAL,
        225 => self::NORMAL,
        257 => self::NORMAL,
        286 => self::NORMAL,
        325 => self::NORMAL,
        388 => self::NORMAL,
        425 => self::NORMAL,
        455 => self::NORMAL,
    ];

    const FP = "fp";

    const FP_ANX = "fp1_anx";
    const FP_ANX_REPONSES = [
        200 => self::NORMAL,
        226 => self::NORMAL,
        251 => self::NORMAL,
        277 => self::NORMAL,
        303 => self::NORMAL,
        329 => self::NORMAL,
        355 => self::NORMAL,
        381 => self::NORMAL,
        407 => self::NORMAL,
        433 => self::NORMAL,
    ];


    const FP_INSTROS = "fp2_instros";
    const FP_INSTROS_REPONSES = [
        210 => self::NORMAL,
        236 => self::NORMAL,
        262 => self::NORMAL,
        288 => self::NORMAL,
        313 => self::NORMAL,
        339 => self::INVERSE,
        366 => self::NORMAL,
        392 => self::INVERSE,
        417 => self::NORMAL,
        443 => self::NORMAL,
    ];

    const FP_HDEP = "fp3_hdep";
    const FP_HDEP_REPONSES = [
        220 => self::NORMAL,
        246 => self::NORMAL,
        272 => self::NORMAL,
        298 => self::NORMAL,
        323 => self::NORMAL,
        350 => self::NORMAL,
        376 => self::NORMAL,
        402 => self::NORMAL,
        428 => self::NORMAL,
        453 => self::NORMAL,
    ];

    const FP_DEV = "fp4_dev";
    const FP_DEV_REPONSES = [
        205 => self::NORMAL,
        231 => self::NORMAL,
        256 => self::NORMAL,
        282 => self::NORMAL,
        308 => self::NORMAL,
        334 => self::NORMAL,
        360 => self::NORMAL,
        386 => self::NORMAL,
        412 => self::NORMAL,
        438 => self::NORMAL,
    ];

    const FP_GEN = "fp5_gen";
    const FP_GEN_REPONSES = [
        205 => self::NORMAL,
        231 => self::NORMAL,
        256 => self::NORMAL,
        282 => self::NORMAL,
        308 => self::NORMAL,
        334 => self::NORMAL,
        360 => self::NORMAL,
        386 => self::NORMAL,
        412 => self::NORMAL,
        438 => self::NORMAL,
    ];


    const ME = "me";

    const ME_CE = "me1_ce";
    const ME_CE_REPONSES = [
        204 => self::INVERSE,
        230 => self::INVERSE,
        255 => self::INVERSE,
        281 => self::INVERSE,
        307 => self::INVERSE,
        333 => self::INVERSE,
        359 => self::INVERSE,
        385 => self::INVERSE,
        411 => self::INVERSE,
        437 => self::INVERSE,
    ];

    const ME_MODES = "me2_modes";
    const ME_MODES_REPONSES = [
        219 => self::INVERSE,
        245 => self::INVERSE,
        271 => self::INVERSE,
        297 => self::INVERSE,
        322 => self::INVERSE,
        349 => self::INVERSE,
        375 => self::INVERSE,
        401 => self::INVERSE,
        427 => self::INVERSE,
        452 => self::INVERSE,
    ];

    const ME_COOPE = "me3_coope";
    const ME_COOPE_REPONSES = [
        199 => self::INVERSE,
        224 => self::NORMAL,
        250 => self::INVERSE,
        276 => self::INVERSE,
        302 => self::INVERSE,
        328 => self::INVERSE,
        354 => self::INVERSE,
        380 => self::INVERSE,
        406 => self::INVERSE,
        432 => self::INVERSE,
    ];

    const ME_AMB = "me4_amb";
    const ME_AMB_REPONSES = [
        214 => self::INVERSE,
        240 => self::INVERSE,
        266 => self::INVERSE,
        292 => self::INVERSE,
        317 => self::INVERSE,
        344 => self::INVERSE,
        370 => self::INVERSE,
        396 => self::INVERSE,
        421 => self::INVERSE,
        447 => self::INVERSE,
    ];

    const ME_DROIT = "me5_droit";
    const ME_DROIT_REPONSES = [
        209 => self::NORMAL,
        235 => self::INVERSE,
        261 => self::INVERSE,
        287 => self::INVERSE,
        312 => self::INVERSE,
        338 => self::INVERSE,
        365 => self::INVERSE,
        391 => self::INVERSE,
        416 => self::INVERSE,
        442 => self::INVERSE,
    ];

    const CP = "cp";

    const CP_FIAB = "cp1_fiab";
    const CP_FIAB_REPONSES = [
        208 => self::NORMAL,
        234 => self::INVERSE,
        260 => self::INVERSE,
        285 => self::NORMAL,
        311 => self::INVERSE,
        337 => self::INVERSE,
        364 => self::NORMAL,
        390 => self::NORMAL,
        415 => self::INVERSE,
        441 => self::INVERSE,
    ];

    const CP_AUTODISC = "cp2_autodisc";
    const CP_AUTODISC_REPONSES = [
        198 => self::NORMAL,
        223 => self::NORMAL,
        249 => self::INVERSE,
        275 => self::NORMAL,
        301 => self::INVERSE,
        327 => self::INVERSE,
        353 => self::INVERSE,
        379 => self::INVERSE,
        405 => self::INVERSE,
        431 => self::NORMAL,
    ];

    const CP_REFL = "cp3_refl";
    const CP_REFL_REPONSES = [
        203 => self::NORMAL,
        229 => self::NORMAL,
        254 => self::INVERSE,
        280 => self::NORMAL,
        306 => self::NORMAL,
        332 => self::NORMAL,
        358 => self::NORMAL,
        384 => self::NORMAL,
        410 => self::NORMAL,
        436 => self::NORMAL,
    ];

    const CP_RIG = "cp4_rig";
    const CP_RIG_REPONSES = [
        218 => self::NORMAL,
        244 => self::NORMAL,
        270 => self::INVERSE,
        296 => self::NORMAL,
        321 => self::NORMAL,
        348 => self::NORMAL,
        374 => self::NORMAL,
        400 => self::INVERSE,
        426 => self::NORMAL,
        451 => self::NORMAL,
    ];

    const CP_SVA = "cp5_sva";
    const CP_SVA_REPONSES = [
        213 => self::NORMAL,
        239 => self::NORMAL,
        265 => self::INVERSE,
        291 => self::NORMAL,
        316 => self::NORMAL,
        343 => self::NORMAL,
        369 => self::INVERSE,
        395 => self::NORMAL,
        420 => self::NORMAL,
        446 => self::INVERSE,
    ];

    const AR = "ar";

    const AR_CONF = "ar1_conf";
    const AR_CONF_REPONSES = [
        211 => self::NORMAL,
        237 => self::INVERSE,
        263 => self::INVERSE,
        289 => self::INVERSE,
        314 => self::INVERSE,
        341 => self::INVERSE,
        367 => self::INVERSE,
        393 => self::INVERSE,
        418 => self::INVERSE,
        444 => self::INVERSE,
    ];

    const AR_GREGA = "ar2_grega";
    const AR_GREGA_REPONSES = [
        206 => self::NORMAL,
        232 => self::NORMAL,
        258 => self::INVERSE,
        283 => self::NORMAL,
        309 => self::NORMAL,
        335 => self::INVERSE,
        362 => self::INVERSE,
        387 => self::INVERSE,
        413 => self::NORMAL,
        439 => self::INVERSE,
    ];

    const AR_SPONT = "ar3_spont";
    const AR_SPONT_REPONSES = [
        221 => self::INVERSE,
        247 => self::INVERSE,
        273 => self::INVERSE,
        299 => self::NORMAL,
        324 => self::INVERSE,
        351 => self::NORMAL,
        377 => self::INVERSE,
        403 => self::NORMAL,
        429 => self::NORMAL,
        454 => self::NORMAL,
    ];
    const AR_NOUV = "ar4_nouv";
    const AR_NOUV_REPONSES = [
        216 => self::NORMAL,
        242 => self::NORMAL,
        268 => self::INVERSE,
        294 => self::NORMAL,
        319 => self::INVERSE,
        346 => self::NORMAL,
        372 => self::NORMAL,
        398 => self::NORMAL,
        423 => self::NORMAL,
        449 => self::INVERSE,
    ];

    const AR_DYN = "ar5_dyn";
    const AR_DYN_REPONSES = [
        201 => self::NORMAL,
        227 => self::NORMAL,
        252 => self::NORMAL,
        278 => self::NORMAL,
        304 => self::NORMAL,
        330 => self::NORMAL,
        356 => self::NORMAL,
        382 => self::INVERSE,
        408 => self::NORMAL,
        434 => self::INVERSE,
    ];

    const PM = "pm";

    const PM_1 = "pm1";
    const PM_1_REPONSES = [
        207 => self::NORMAL,
        233 => self::INVERSE,
        259 => self::NORMAL,
        284 => self::NORMAL,
        310 => self::INVERSE,
        336 => self::NORMAL,
        363 => self::NORMAL,
        389 => self::INVERSE,
        414 => self::INVERSE,
        440 => self::NORMAL,
    ];

    const PM_AFFIRM = "pm2_affirm";
    const PM_AFFIRM_REPONSES = [
        217 => self::NORMAL,
        243 => self::INVERSE,
        269 => self::INVERSE,
        295 => self::INVERSE,
        320 => self::NORMAL,
        347 => self::INVERSE,
        373 => self::INVERSE,
        399 => self::INVERSE,
        424 => self::INVERSE,
        450 => self::INVERSE,
    ];
    const PM_EMP = "pm3_emp";
    const PM_EMP_REPONSES = [
        202 => self::NORMAL,
        228 => self::INVERSE,
        253 => self::NORMAL,
        279 => self::INVERSE,
        305 => self::NORMAL,
        331 => self::INVERSE,
        357 => self::NORMAL,
        383 => self::INVERSE,
        409 => self::INVERSE,
        435 => self::NORMAL,
    ];
    const PM_ING = "pm4_ing";
    const PM_ING_REPONSES = [
        212 => self::NORMAL,
        238 => self::NORMAL,
        264 => self::NORMAL,
        290 => self::NORMAL,
        315 => self::NORMAL,
        342 => self::NORMAL,
        368 => self::NORMAL,
        394 => self::NORMAL,
        419 => self::NORMAL,
        445 => self::INVERSE,
    ];
    const PM_SINTEL = "pm5_sintel";
    const PM_SINTEL_REPONSES = [
        197 => self::NORMAL,
        222 => self::NORMAL,
        248 => self::NORMAL,
        274 => self::NORMAL,
        300 => self::NORMAL,
        326 => self::NORMAL,
        352 => self::NORMAL,
        378 => self::NORMAL,
        404 => self::NORMAL,
        430 => self::NORMAL,
    ];

    const RC = "rc";

    const ALL_PERSONNALITE = (
        self::FP_ANX_REPONSES +
        self::FP_INSTROS_REPONSES +
        self::FP_HDEP_REPONSES +
        self::FP_DEV_REPONSES +
        self::FP_GEN_REPONSES +
        self::ME_CE_REPONSES +
        self::ME_MODES_REPONSES +
        self::ME_COOPE_REPONSES +
        self::ME_AMB_REPONSES +
        self::ME_DROIT_REPONSES +
        self::CP_FIAB_REPONSES +
        self::CP_AUTODISC_REPONSES +
        self::CP_REFL_REPONSES +
        self::CP_RIG_REPONSES +
        self::CP_SVA_REPONSES +
        self::AR_CONF_REPONSES +
        self::AR_GREGA_REPONSES +
        self::AR_SPONT_REPONSES +
        self::AR_NOUV_REPONSES +
        self::AR_DYN_REPONSES +
        self::PM_1_REPONSES +
        self::PM_AFFIRM_REPONSES +
        self::PM_EMP_REPONSES +
        self::PM_ING_REPONSES +
        self::PM_SINTEL_REPONSES
    );
}



