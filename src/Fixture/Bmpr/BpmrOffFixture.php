<?php

namespace App\Fixture\Bmpr;

use App\Entity\Concours;
use App\Entity\Echelle;
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
        $concours = new Concours(
            0,
            self::CONCOURS_NOM,
            new ArrayCollection(),
            new ArrayCollection(),
            GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            "[Type concours]",
            "[Version batterie]",
            new ArrayCollection()
        );

        $session = $this->session_exemple(
            $concours,
            $manager->getRepository(Sgap::class)->findOneBy([]),
            $manager->getRepository(NiveauScolaire::class)->findOneBy([]),
        );

        $this->questions($concours);

        $profil = new Profil(
            0,
            self::PROFIL_NOM,
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection(),
            new ArrayCollection()
        );

        $this->vt($profil);
        $this->sp($profil);
        $this->rais($profil);
        $this->cv($profil);
        $this->sl($profil);
        $this->dic($profil);
        $this->as($profil);
        $this->eg($profil);
        $this->qr($profil);
        $this->at($profil);
        $this->ds($profil);
        $this->fp($profil);
        $this->me($profil);
        $this->cp($profil);
        $this->ar($profil);
        $this->pm($profil);
        $this->rc($profil);

        $manager->persist($concours);
        $manager->persist($profil);
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

        foreach (BpmrOffCorrecteurFixture::ALL_APTITUDES_COGNITIVES as $index => $vrai) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                QuestionConcours::TYPE_VRAI_FAUX
            ));
        }

        foreach (BpmrOffCorrecteurFixture::ALL_PERSONNALITE_INDEX_TO_TYPE as $index => $type
        ) {
            $concours->questions->add(new QuestionConcours(
                0,
                $index,
                $concours,
                QuestionConcours::TYPE_SCORE
            ));
        }

    }

    private function vt(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "VT",
            self::VT_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "VT mauvaises réponses",
            self::VT_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function sp(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "SP",
            self::SP_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "SP mauvaises réponses",
            self::SP_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function rais(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "Rais",
            self::RAIS_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "Rais mauvaises réponses",
            self::RAIS_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function cv(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "CV",
            self::CV_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "CV mauvaises réponses",
            self::CV_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function sl(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "SL",
            self::SL_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "SL mauvaises réponses",
            self::SL_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function dic(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "DIC",
            self::DIC_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "DIC mauvaises réponses",
            self::DIC_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function as(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "AS",
            self::AS_BR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
        $profil->echelles->add(new Echelle(
            0,
            "AS Mauvaises réponses",
            self::AS_MR,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(), new ArrayCollection()));
    }

    private function eg(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "EG",
            self::EG,
            Echelle::TYPE_ECHELLE_COMPOSITE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    private function qr(
        Profil $profil
    )
    {
        $profil->echelles->add(new Echelle(
            0,
            "QR",
            self::QR,
            Echelle::TYPE_ECHELLE_COMPOSITE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    private function at(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "AT",
            self::AT,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    private function ds(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "DS",
            self::DS,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    private function fp(Profil $profil)
    {
        $profil->echelles->add(new Echelle(0, "Anx", self::FP_ANX, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Intros", self::FP_INTROS, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "H.Dép", self::FP_HDEP, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Dév", self::FP_DEV, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Gên", self::FP_GEN, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "FP", self::FP, Echelle::TYPE_ECHELLE_COMPOSITE));
    }

    private function me(Profil $profil)
    {
        $profil->echelles->add(new Echelle(0, "CE", self::ME_CE, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Hu", self::ME_HU, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Coope", self::ME_COOPE, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Amb", self::ME_AMB, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Droit", self::ME_DROIT, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "ME", self::ME, Echelle::TYPE_ECHELLE_COMPOSITE));
    }

    private function cp(Profil $profil)
    {
        $profil->echelles->add(new Echelle(0, "Fiab", self::CP_FIAB, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Autodisc", self::CP_AUTODISC, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Refl", self::CP_REFL, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Rig", self::CP_RIG, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "S.Va", self::CP_SVA, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "CP", self::CP, Echelle::TYPE_ECHELLE_COMPOSITE));
    }

    private function ar(Profil $profil)
    {
        $profil->echelles->add(new Echelle(0, "Conf", self::AR_CONF, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Gréga", self::AR_GREGA, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Spont", self::AR_SPONT, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Nouv", self::AR_NOUV, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Dyn", self::AR_DYN, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "AR", self::AR, Echelle::TYPE_ECHELLE_COMPOSITE));
    }

    private function pm(Profil $profil)
    {
        $profil->echelles->add(new Echelle(0, "Lead", self::PM_LEAD, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Affirm", self::PM_AFFIRM, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Emp", self::PM_EMP, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "Ingé", self::PM_ING, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "S.Intel", self::PM_SINTEL, Echelle::TYPE_ECHELLE_SIMPLE));
        $profil->echelles->add(new Echelle(0, "PM", self::PM, Echelle::TYPE_ECHELLE_COMPOSITE));
    }

    private function rc(Profil $profil)
    {
        $profil->echelles->add(new Echelle(
            0,
            "rc",
            self::RC,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    const CONCOURS_NOM = "Concours BPMR - Officier";
    const PROFIL_NOM = "BPMR - Officier";

    const INDEX_EXEMPLES = [1, 52, 53, 74, 75, 76, 92, 113, 129, 150];

    const EG = "eg";
    const QR = "qr";
    const VT_BR = "vt_br";
    const VT_MR = "vt_mr";
    const SP_BR = "sp_br";
    const SP_MR = "sp_mr";
    const RAIS_BR = "rais_br";
    const RAIS_MR = "rais_mr";
    const CV_BR = "cv_br";
    const CV_MR = "cv_mr";
    const SL_BR = "sl_br";
    const SL_MR = "sl_mr";
    const DIC_BR = "dic_br";
    const DIC_MR = "dic_mr";
    const AS_BR = "as_br";
    const AS_MR = "as_mr";

    const APTITUDES_COGNITIVES_ALL_BR = [
        self::VT_BR,
        self::SP_BR,
        self::RAIS_BR,
        self::CV_BR,
        self::SL_BR,
        self::DIC_BR,
        self::AS_BR
    ];

    const APTITUDES_COGNITIVES_BR_TO_MR = [
        self::VT_BR => self::VT_MR,
        self::SP_BR => self::SP_MR,
        self::RAIS_BR => self::RAIS_MR,
        self::CV_BR => self::CV_MR,
        self::SL_BR => self::SL_MR,
        self::DIC_BR => self::DIC_MR,
        self::AS_BR => self::AS_MR
    ];

    const AT = "at";
    const DS = "ds";
    const FP = "fp";
    const FP_ANX = "fp1_anx";
    const FP_INTROS = "fp2_instros";
    const FP_HDEP = "fp3_hdep";
    const FP_DEV = "fp4_dev";
    const FP_GEN = "fp5_gen";
    const ME = "me";
    const ME_CE = "me1_ce";
    const ME_HU = "me2_modes";
    const ME_COOPE = "me3_coope";
    const ME_AMB = "me4_amb";
    const ME_DROIT = "me5_droit";
    const CP = "cp";
    const CP_FIAB = "cp1_fiab";
    const CP_AUTODISC = "cp2_autodisc";
    const CP_REFL = "cp3_refl";
    const CP_RIG = "cp4_rig";
    const CP_SVA = "cp5_sva";
    const AR = "ar";
    const AR_CONF = "ar1_conf";
    const AR_GREGA = "ar2_grega";
    const AR_SPONT = "ar3_spont";
    const AR_NOUV = "ar4_nouv";
    const AR_DYN = "ar5_dyn";
    const PM = "pm";
    const PM_LEAD = "pm1_lead";
    const PM_AFFIRM = "pm2_affirm";
    const PM_EMP = "pm3_emp";
    const PM_ING = "pm4_ing";
    const PM_SINTEL = "pm5_sintel";
    const RC = "rc";

    const NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE= [
        BpmrOffFixture::FP => [
            BpmrOffFixture::FP_ANX,
            BpmrOffFixture::FP_INTROS,
            BpmrOffFixture::FP_HDEP,
            BpmrOffFixture::FP_DEV,
            BpmrOffFixture::FP_GEN,
        ],
        BpmrOffFixture::ME => [
            BpmrOffFixture::ME_CE,
            BpmrOffFixture::ME_HU,
            BpmrOffFixture::ME_COOPE,
            BpmrOffFixture::ME_AMB,
            BpmrOffFixture::ME_DROIT,
        ],
        BpmrOffFixture::CP => [
            BpmrOffFixture::CP_FIAB,
            BpmrOffFixture::CP_AUTODISC,
            BpmrOffFixture::CP_REFL,
            BpmrOffFixture::CP_RIG,
            BpmrOffFixture::CP_SVA,
        ],
        BpmrOffFixture::AR => [
            BpmrOffFixture::AR_CONF,
            BpmrOffFixture::AR_GREGA,
            BpmrOffFixture::AR_SPONT,
            BpmrOffFixture::AR_NOUV,
            BpmrOffFixture::AR_DYN,
        ],
        BpmrOffFixture::PM => [
            BpmrOffFixture::PM_LEAD,
            BpmrOffFixture::PM_AFFIRM,
            BpmrOffFixture::PM_EMP,
            BpmrOffFixture::PM_ING,
            BpmrOffFixture::PM_SINTEL,
        ],
    ];
}



