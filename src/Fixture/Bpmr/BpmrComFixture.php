<?php

namespace App\Fixture\Bpmr;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\Graphique;
use App\Entity\Profil;
use App\Entity\QuestionConcours;
use App\Entity\Subtest;
use Doctrine\Common\Collections\ArrayCollection;

class BpmrComFixture extends AbstractBpmrFixture
{

    public function __construct()
    {
        parent::__construct(
            468,
            self::CONCOURS_NOM,
            self::PROFIL_NOM,
            self::CORRECTEUR_NOM,
            self::ETALONNAGE_NOM,
            9,
            self::GRAPHIQUE_NOM
        );
    }

    protected function aptitudesCognitives(Profil $profil)
    {
        $this->echellesSimplesAptitudesCognitives($profil, self::APTITUDES_COGNITIVES_NOM_PHP_TO_NOM);

        $profil->echelles->add(new Echelle(
            0,
            "EG",
            self::EG,
            Echelle::TYPE_ECHELLE_COMPOSITE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
        $profil->echelles->add(new Echelle(
            0,
            "QR",
            self::QR,
            Echelle::TYPE_ECHELLE_COMPOSITE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    protected function personnalite(Profil $profil)
    {
        $this->echellesSimplesEtCompositesPersonnalite(
            $profil,
            self::PERSONNALITE_NOM_PHP_TO_NOM_SIMPLE,
            self::PERSONNALITE_NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE
        );

        $profil->echelles->add(new Echelle(
            0,
            "DS",
            self::DS,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));

        $profil->echelles->add(new Echelle(
            0,
            "AT",
            self::AT,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));

        $profil->echelles->add(new Echelle(
            0,
            "RC",
            self::RC,
            Echelle::TYPE_ECHELLE_SIMPLE,
            new ArrayCollection(),
            new ArrayCollection()
        ));
    }

    protected function questions(Concours $concours)
    {
        $this->questionsTypeIndexAsValue($concours, self::INDEX_EXEMPLES, QuestionConcours::TYPE_EXEMPLE);
        $this->questionsTypeIndexAsKey($concours,
            self::ALL_APTITUDES_COGNITIVES,
            QuestionConcours::TYPE_VRAI_FAUX);
        $this->questionsTypeIndexAsKey($concours,
            self::ALL_PERSONNALITE_INDEX_TO_TYPE,
            QuestionConcours::TYPE_SCORE);
    }

    private function correcteurEg(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $echelle = $this->findEchelleInProfil($profil, self::EG);
        $expression = "((" . $this->nombreBonnesReponsesCognitif() . ")*(" . $this->nombreReponsesTraiteesCognitif() . ")) ** 0.5";

        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    private function correcteurQr(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $echelle = $this->findEchelleInProfil($profil, self::QR);
        $expression = "((" . $this->nombreBonnesReponsesCognitif() . ")/(" . $this->nombreReponsesTraiteesCognitif() . ")) * 100";

        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    private function nombreReponsesTraiteesCognitif(): string
    {
        $nombre_reponses_traitees = "0";
        foreach (self::ALL_APTITUDES_COGNITIVES as $index => $vrai) {
            $nombre_reponses_traitees = $nombre_reponses_traitees . "+repondu(" . $index . ")";
        }

        return $nombre_reponses_traitees;
    }

    private function nombreBonnesReponsesCognitif(): string
    {
        $nombre_bonnes_reponses = "0";

        foreach (self::APTITUDES_COGNITIVES_NOM_PHP_BR as $echelle) {
            $nombre_bonnes_reponses = $nombre_bonnes_reponses . "+echelle(\"" . $echelle . "\")";
        }

        return $nombre_bonnes_reponses;
    }

    private function correcteurAt(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $correcteur->echelles->add($this->echelleCorrecteur(
            $correcteur,
            $this->findEchelleInProfil($profil, self::AT),
            self::AT_REPONSES
        ));
    }

    private function correcteurDs(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $correcteur->echelles->add($this->echelleCorrecteur(
            $correcteur,
            $this->findEchelleInProfil($profil, self::DS),
            self::DS_REPONSES
        ));
    }

    private function correcteurRc(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $expression = "0";
        foreach (self::ALL_PERSONNALITE_INDEX_TO_TYPE as $index => $type) {
            $expression = $expression . "+vraiC(" . $index . ")";
        }

        $echelle = $this->findEchelleInProfil($profil, self::RC);
        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    protected function correcteurAptitudesCognitives(Profil $profil, Correcteur $correcteur)
    {
        $this->echellesCorrecteurAptitudeCognitive($profil, $correcteur, self::VRAI_NOM_PHP_TO_INDEX_VRAI, "vrai");
        $this->echellesCorrecteurAptitudeCognitive($profil, $correcteur, self::FAUX_NOM_PHP_TO_INDEX_VRAI, "faux");
        $this->correcteurEg($profil, $correcteur);
        $this->correcteurQr($profil, $correcteur);
    }

    protected function correcteurPersonnalite(Profil $profil, Correcteur $correcteur)
    {
        $this->echellesCorrecteurPersonnalite($profil,
            $correcteur,
            self::NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE_TO_INDEX_TO_TYPE);
        $this->correcteurAt($profil, $correcteur);
        $this->correcteurDs($profil, $correcteur);
        $this->correcteurRc($profil, $correcteur);
    }

    private function subtestAptitudesCognitives(Graphique $graphique): Subtest
    {
        $echelles_bas_de_cadre = array(
            array($this->findEchelleInGraphique($graphique,
                self::EG)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
            array($this->findEchelleInGraphique($graphique,
                self::QR)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
        );

        $echelles_core = [];

        foreach (self::APTITUDES_COGNITIVES_BR_TO_MR as $br => $mr) {
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
            array($this->findEchelleInGraphique($graphique,
                self::DS)->id, Subtest::TYPE_FOOTER_SCORE_AND_CLASSE),
            array($this->findEchelleInGraphique($graphique, self::AT)->id, Subtest::TYPE_FOOTER_SCORE_ONLY),
            array($this->findEchelleInGraphique($graphique, self::RC)->id, Subtest::TYPE_FOOTER_SCORE_ONLY),
        );

        $echelles_core = array();

        foreach (self::PERSONNALITE_NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE as $nom_php_composite => $noms_php_simples) {

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

    protected function subtests(Graphique $graphique)
    {
        $graphique->subtests->add($this->subtestAptitudesCognitives($graphique));
        $graphique->subtests->add($this->subtestPersonnalite($graphique));
    }


    const CONCOURS_NOM = "Concours BPMR - Commissaire";
    const PROFIL_NOM = "BPMR - Commissaire";
    const CORRECTEUR_NOM = "Correcteur par défaut BPMR - Comissaire";
    const ETALONNAGE_NOM = "Etalonnage de test BPMR - Comissaire";
    const GRAPHIQUE_NOM = "Graphique par défaut BPMR - Comissaire";

    const INDEX_EXEMPLES = [1, 52, 73, 98, 99, 100, 116, 141, 162];

    const EG = "eg";
    const QR = "qr";
    const AS_BR = "as_br";
    const AS_MR = "as_mr";
    const DIC_BR = "dic_br";
    const DIC_MR = "dic_mr";

    const VB_BR = "vb_br";
    const VB_MR = "vb_mr";

    const RAIS_BR = "rais_br";
    const RAIS_MR = "rais_mr";

    const CV_BR = "cv_br";
    const CV_MR = "cv_mr";
    const RM_BR = "rm_br";
    const RM_MR = "rm_mr";
    const VT_BR = "vt_br";
    const VT_MR = "vt_mr";

    const APTITUDES_COGNITIVES_NOM_PHP_TO_NOM = [
        self::AS_BR => "AS",
        self::DIC_BR => "DIC",
        self::VB_BR => "VB",
        self::RAIS_BR => "Rais",
        self::CV_BR => "CV",
        self::RM_BR => "RM",
        self::VT_BR => "VT",
        self::AS_MR => "AS Mauvaises réponses",
        self::DIC_MR => "DIC Mauvaises réponses",
        self::VB_MR => "VB Mauvaises réponses",
        self::RAIS_MR => "Rais Mauvaises réponses",
        self::CV_MR => "CV Mauvaises réponses",
        self::RM_MR => "RM Mauvaises réponses",
        self::VT_MR => "VT Mauvaises réponses",
    ];

    const APTITUDES_COGNITIVES_NOM_PHP_BR = [
        self::AS_BR,
        self::DIC_BR,
        self::VB_BR,
        self::RAIS_BR,
        self::CV_BR,
        self::RM_BR,
        self::VT_BR
    ];

    const APTITUDES_COGNITIVES_BR_TO_MR = [
        self::AS_BR => self::AS_MR,
        self::DIC_BR => self::DIC_MR,
        self::VB_BR => self::VB_MR,
        self::RAIS_BR => self::RAIS_MR,
        self::CV_BR => self::CV_MR,
        self::RM_BR => self::RM_MR,
        self::VT_BR => self::VT_MR
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
    const ME_MODES = "me2_modes";
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

    const PERSONNALITE_NOM_PHP_TO_NOM_SIMPLE = [
        self::FP => "FP",
        self::FP_ANX => "Anx",
        self::FP_INTROS => "Intros",
        self::FP_HDEP => "H.Dep",
        self::FP_DEV => "Dev",
        self::FP_GEN => "Gen",
        self::ME => "ME",
        self::ME_CE => "CE",
        self::ME_MODES => "Modes",
        self::ME_COOPE => "Coopé",
        self::ME_AMB => "Amb",
        self::ME_DROIT => "Droit",
        self::CP => "CP",
        self::CP_FIAB => "Fiab",
        self::CP_AUTODISC => "Autodisc",
        self::CP_REFL => "Refl",
        self::CP_RIG => "Rig",
        self::CP_SVA => "Sva",
        self::AR => "Ar",
        self::AR_CONF => "Conf",
        self::AR_GREGA => "Grega",
        self::AR_SPONT => "Spont",
        self::AR_NOUV => "Nouv",
        self::AR_DYN => "Dyn",
        self::PM => "PM",
        self::PM_LEAD => "Lead",
        self::PM_AFFIRM => "Affirm",
        self::PM_EMP => "Emp",
        self::PM_ING => "Ing",
        self::PM_SINTEL => "S.Intel"
    ];

    const PERSONNALITE_NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE = [
        self::FP => [
            self::FP_ANX,
            self::FP_INTROS,
            self::FP_HDEP,
            self::FP_DEV,
            self::FP_GEN,
        ],
        self::ME => [
            self::ME_CE,
            self::ME_MODES,
            self::ME_COOPE,
            self::ME_AMB,
            self::ME_DROIT,
        ],
        self::CP => [
            self::CP_FIAB,
            self::CP_AUTODISC,
            self::CP_REFL,
            self::CP_RIG,
            self::CP_SVA,
        ],
        self::AR => [
            self::AR_CONF,
            self::AR_GREGA,
            self::AR_SPONT,
            self::AR_NOUV,
            self::AR_DYN,
        ],
        self::PM => [
            self::PM_LEAD,
            self::PM_AFFIRM,
            self::PM_EMP,
            self::PM_ING,
            self::PM_SINTEL,
        ],
    ];

    const A = "A";
    const B = "B";
    const C = "C";
    const D = "D";
    const E = "E";

    const VRAI_NOM_PHP_TO_INDEX_VRAI = [
        self::AS_BR => self::REPONSES_AS_INDEX_TO_VRAI,
        self::DIC_BR => self::REPONSES_DIC_INDEX_TO_VRAI,
        self::VB_BR => self::REPONSES_VB_INDEX_TO_VRAI,
        self::RAIS_BR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        self::CV_BR => self::REPONSES_CV_INDEX_TO_VRAI,
        self::RM_BR => self::REPONSES_RM_INDEX_TO_VRAI,
        self::VT_BR => self::REPONSES_VT_INDEX_TO_VRAI,
    ];

    const FAUX_NOM_PHP_TO_INDEX_VRAI = [
        self::AS_MR => self::REPONSES_AS_INDEX_TO_VRAI,
        self::DIC_MR => self::REPONSES_DIC_INDEX_TO_VRAI,
        self::VB_MR => self::REPONSES_VB_INDEX_TO_VRAI,
        self::RAIS_MR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        self::CV_MR => self::REPONSES_CV_INDEX_TO_VRAI,
        self::RM_MR => self::REPONSES_RM_INDEX_TO_VRAI,
        self::VT_MR => self::REPONSES_VT_INDEX_TO_VRAI,
    ];

    const REPONSES_AS_INDEX_TO_VRAI = [
        2 => self::A,
        3 => self::A,
        4 => self::B,
        5 => self::A,
        6 => self::A,
        7 => self::B,
        8 => self::A,
        9 => self::B,
        10 => self::B,
        11 => self::B,
        12 => self::A,
        13 => self::A,
        14 => self::A,
        15 => self::B,
        16 => self::B,
        17 => self::B,
        18 => self::A,
        19 => self::A,
        20 => self::B,
        21 => self::A,
        22 => self::B,
        23 => self::A,
        24 => self::B,
        25 => self::B,
        26 => self::B,
        27 => self::B,
        28 => self::A,
        29 => self::A,
        30 => self::B,
        31 => self::A,
        32 => self::B,
        33 => self::B,
        34 => self::B,
        35 => self::A,
        36 => self::A,
        37 => self::B,
        38 => self::B,
        39 => self::B,
        40 => self::A,
        41 => self::B,
        42 => self::B,
        43 => self::A,
        44 => self::B,
        45 => self::B,
        46 => self::B,
        47 => self::A,
        48 => self::A,
        49 => self::B,
        50 => self::B,
        51 => self::A,

    ];

    const REPONSES_DIC_INDEX_TO_VRAI = [
        53 => self::D,
        54 => self::D,
        55 => self::E,
        56 => self::B,
        57 => self::C,
        58 => self::A,
        59 => self::C,
        60 => self::C,
        61 => self::C,
        62 => self::A,
        63 => self::E,
        64 => self::D,
        65 => self::E,
        66 => self::E,
        67 => self::B,
        68 => self::E,
        69 => self::E,
        70 => self::D,
        71 => self::D,
        72 => self::D,
    ];

    const REPONSES_VB_INDEX_TO_VRAI = [
        74 => self::C,
        75 => self::A,
        76 => self::B,
        77 => self::E,
        78 => self::E,
        79 => self::D,
        80 => self::B,
        81 => self::C,
        82 => self::B,
        83 => self::C,
        84 => self::C,
        85 => self::E,
        86 => self::A,
        87 => self::E,
        88 => self::C,
        89 => self::E,
        90 => self::E,
        91 => self::B,
        92 => self::D,
        93 => self::A,
        94 => self::B,
        95 => self::D,
        96 => self::C,
        97 => self::E,
    ];

    const REPONSES_RAIS_INDEX_TO_VRAI = [
        101 => self::D,
        102 => self::C,
        103 => self::C,
        104 => self::B,
        105 => self::C,
        106 => self::B,
        107 => self::B,
        108 => self::D,
        109 => self::A,
        110 => self::C,
        111 => self::C,
        112 => self::A,
        113 => self::A,
        114 => self::B,
        115 => self::C,
    ];

    const REPONSES_CV_INDEX_TO_VRAI = [
        117 => self::B,
        118 => self::A,
        119 => self::B,
        120 => self::C,
        121 => self::B,
        122 => self::E,
        123 => self::C,
        124 => self::D,
        125 => self::C,
        126 => self::E,
        127 => self::B,
        128 => self::E,
        129 => self::B,
        130 => self::A,
        131 => self::D,
        132 => self::D,
        133 => self::C,
        134 => self::E,
        135 => self::E,
        136 => self::B,
        137 => self::C,
        138 => self::A,
        139 => self::C,
        140 => self::E,
    ];

    const REPONSES_RM_INDEX_TO_VRAI = [
        142 => self::C,
        143 => self::D,
        144 => self::B,
        145 => self::B,
        146 => self::D,
        147 => self::C,
        148 => self::B,
        149 => self::A,
        150 => self::A,
        151 => self::D,
        152 => self::C,
        153 => self::D,
        154 => self::D,
        155 => self::D,
        156 => self::B,
        157 => self::C,
        158 => self::B,
        159 => self::D,
        160 => self::A,
        161 => self::D,
    ];

    const REPONSES_VT_INDEX_TO_VRAI = [
        163 => self::A,
        164 => self::A,
        165 => self::B,
        166 => self::B,
        167 => self::B,
        168 => self::A,
        169 => self::B,
        170 => self::A,
        171 => self::B,
        172 => self::B,
        173 => self::A,
        174 => self::B,
        175 => self::A,
        176 => self::A,
        177 => self::B,
        178 => self::B,
        179 => self::A,
        180 => self::B,
        181 => self::B,
        182 => self::A,
        183 => self::A,
        184 => self::B,
        185 => self::B,
        186 => self::A,
        187 => self::B,
        188 => self::B,
        189 => self::A,
        190 => self::B,
        191 => self::A,
        192 => self::A,
        193 => self::A,
        194 => self::B,
        195 => self::A,
        196 => self::B,
        197 => self::B,
        198 => self::B,
        199 => self::B,
        200 => self::A,
        201 => self::B,
        202 => self::A,
        203 => self::A,
        204 => self::B,
        205 => self::A,
        206 => self::B,
        207 => self::A,
    ];

    const ALL_APTITUDES_COGNITIVES = self::REPONSES_AS_INDEX_TO_VRAI +
    self::REPONSES_DIC_INDEX_TO_VRAI +
    self::REPONSES_VB_INDEX_TO_VRAI +
    self::REPONSES_RAIS_INDEX_TO_VRAI +
    self::REPONSES_CV_INDEX_TO_VRAI +
    self::REPONSES_RM_INDEX_TO_VRAI +
    self::REPONSES_VT_INDEX_TO_VRAI;

    const NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE_TO_INDEX_TO_TYPE = [
        self::FP => [
            self::FP_ANX => self::FP_ANX_REPONSES,
            self::FP_INTROS => self::FP_INTROS_REPONSES,
            self::FP_HDEP => self::FP_HDEP_REPONSES,
            self::FP_DEV => self::FP_DEV_REPONSES,
            self::FP_GEN => self::FP_GEN_REPONSES,
        ],
        self::ME => [
            self::ME_CE => self::ME_CE_REPONSES,
            self::ME_MODES => self::ME_MODES_REPONSES,
            self::ME_COOPE => self::ME_COOPE_REPONSES,
            self::ME_AMB => self::ME_AMB_REPONSES,
            self::ME_DROIT => self::ME_DROIT_REPONSES,
        ],
        self::CP => [
            self::CP_FIAB => self::CP_FIAB_REPONSES,
            self::CP_AUTODISC => self::CP_AUTODISC_REPONSES,
            self::CP_REFL => self::CP_REFL_REPONSES,
            self::CP_RIG => self::CP_RIG_REPONSES,
            self::CP_SVA => self::CP_SVA_REPONSES,
        ],
        self::AR => [
            self::AR_CONF => self::AR_CONF_REPONSES,
            self::AR_GREGA => self::AR_GREGA_REPONSES,
            self::AR_SPONT => self::AR_SPONT_REPONSES,
            self::AR_NOUV => self::AR_NOUV_REPONSES,
            self::AR_DYN => self::AR_DYN_REPONSES,
        ],
        self::PM => [
            self::PM_LEAD => self::PM_LEAD_REPONSES,
            self::PM_AFFIRM => self::PM_AFFIRM_REPONSES,
            self::PM_EMP => self::PM_EMP_REPONSES,
            self::PM_ING => self::PM_ING_REPONSES,
            self::PM_SINTEL => self::PM_SINTEL_REPONSES,
        ],
    ];

    const NORMAL = "score43210";
    const INVERSE = "score01234";

    const AT_REPONSES = [
        208 => self::NORMAL,
        353 => self::NORMAL,
        468 => self::NORMAL
    ];

    const DS_REPONSES = [
        234 => self::NORMAL,
        260 => self::NORMAL,
        286 => self::NORMAL,
        312 => self::NORMAL,
        338 => self::NORMAL,
        365 => self::NORMAL,
        391 => self::NORMAL,
        417 => self::NORMAL,
    ];

    const FP_ANX_REPONSES = [
        211 => self::NORMAL,
        237 => self::NORMAL,
        263 => self::NORMAL,
        289 => self::NORMAL,
        315 => self::NORMAL,
        341 => self::NORMAL,
        368 => self::NORMAL,
        394 => self::NORMAL,
        420 => self::NORMAL,
        445 => self::NORMAL,
    ];


    const FP_INTROS_REPONSES = [
        231 => self::NORMAL,
        257 => self::NORMAL,
        283 => self::NORMAL,
        309 => self::NORMAL,
        335 => self::NORMAL,
        362 => self::INVERSE,
        388 => self::NORMAL,
        414 => self::INVERSE,
        440 => self::NORMAL,
        465 => self::NORMAL,
    ];

    const FP_HDEP_REPONSES = [
        221 => self::NORMAL,
        247 => self::NORMAL,
        273 => self::NORMAL,
        299 => self::NORMAL,
        325 => self::NORMAL,
        351 => self::NORMAL,
        378 => self::NORMAL,
        404 => self::NORMAL,
        430 => self::NORMAL,
        455 => self::NORMAL,
    ];

    const FP_DEV_REPONSES = [
        216 => self::NORMAL,
        242 => self::NORMAL,
        268 => self::NORMAL,
        294 => self::NORMAL,
        320 => self::NORMAL,
        346 => self::NORMAL,
        373 => self::NORMAL,
        399 => self::NORMAL,
        425 => self::NORMAL,
        450 => self::NORMAL,
    ];

    const FP_GEN_REPONSES = [
        226 => self::NORMAL,
        252 => self::NORMAL,
        278 => self::NORMAL,
        304 => self::NORMAL,
        330 => self::NORMAL,
        357 => self::NORMAL,
        383 => self::NORMAL,
        409 => self::NORMAL,
        435 => self::NORMAL,
        460 => self::NORMAL,
    ];


    const ME_CE_REPONSES = [
        215 => self::INVERSE,
        241 => self::INVERSE,
        267 => self::INVERSE,
        293 => self::INVERSE,
        319 => self::INVERSE,
        345 => self::INVERSE,
        372 => self::INVERSE,
        398 => self::INVERSE,
        424 => self::INVERSE,
        449 => self::INVERSE,
    ];

    const ME_MODES_REPONSES = [
        225 => self::INVERSE,
        251 => self::INVERSE,
        277 => self::INVERSE,
        303 => self::INVERSE,
        329 => self::INVERSE,
        356 => self::INVERSE,
        382 => self::INVERSE,
        408 => self::INVERSE,
        434 => self::INVERSE,
        459 => self::INVERSE,
    ];

    const ME_COOPE_REPONSES = [
        210 => self::INVERSE,
        236 => self::INVERSE,
        262 => self::NORMAL,
        288 => self::INVERSE,
        314 => self::INVERSE,
        340 => self::INVERSE,
        367 => self::INVERSE,
        393 => self::INVERSE,
        419 => self::INVERSE,
        444 => self::INVERSE,
    ];

    const ME_AMB_REPONSES = [
        230 => self::NORMAL,
        256 => self::NORMAL,
        282 => self::NORMAL,
        308 => self::NORMAL,
        334 => self::NORMAL,
        361 => self::NORMAL,
        387 => self::NORMAL,
        413 => self::NORMAL,
        439 => self::NORMAL,
        464 => self::NORMAL,
    ];

    const ME_DROIT_REPONSES = [
        220 => self::NORMAL,
        246 => self::INVERSE,
        272 => self::INVERSE,
        298 => self::INVERSE,
        324 => self::INVERSE,
        350 => self::INVERSE,
        377 => self::INVERSE,
        403 => self::INVERSE,
        429 => self::INVERSE,
        454 => self::INVERSE,
    ];

    const CP_FIAB_REPONSES = [
        209 => self::NORMAL,
        235 => self::NORMAL,
        261 => self::INVERSE,
        287 => self::INVERSE,
        313 => self::NORMAL,
        339 => self::INVERSE,
        366 => self::INVERSE,
        392 => self::NORMAL,
        418 => self::INVERSE,
        443 => self::INVERSE,
    ];

    const CP_AUTODISC_REPONSES = [
        229 => self::NORMAL,
        255 => self::NORMAL,
        281 => self::INVERSE,
        307 => self::NORMAL,
        333 => self::INVERSE,
        360 => self::INVERSE,
        386 => self::INVERSE,
        412 => self::INVERSE,
        438 => self::INVERSE,
        463 => self::NORMAL,
    ];

    const CP_REFL_REPONSES = [
        224 => self::NORMAL,
        250 => self::NORMAL,
        276 => self::INVERSE,
        302 => self::NORMAL,
        328 => self::NORMAL,
        355 => self::NORMAL,
        381 => self::NORMAL,
        407 => self::NORMAL,
        433 => self::NORMAL,
        458 => self::NORMAL,
    ];

    const CP_RIG_REPONSES = [
        214 => self::NORMAL,
        240 => self::NORMAL,
        266 => self::INVERSE,
        292 => self::NORMAL,
        318 => self::NORMAL,
        344 => self::NORMAL,
        371 => self::NORMAL,
        397 => self::INVERSE,
        423 => self::NORMAL,
        448 => self::NORMAL,
    ];

    const CP_SVA_REPONSES = [
        219 => self::NORMAL,
        245 => self::NORMAL,
        271 => self::INVERSE,
        297 => self::NORMAL,
        323 => self::NORMAL,
        349 => self::NORMAL,
        376 => self::INVERSE,
        402 => self::NORMAL,
        428 => self::NORMAL,
        453 => self::INVERSE,
    ];

    const AR_CONF_REPONSES = [
        244 => self::INVERSE,
        270 => self::INVERSE,
        296 => self::INVERSE,
        322 => self::INVERSE,
        348 => self::INVERSE,
        375 => self::INVERSE,
        401 => self::INVERSE,
        427 => self::INVERSE,
        452 => self::INVERSE,
        218 => self::NORMAL,
    ];

    const AR_GREGA_REPONSES = [
        223 => self::NORMAL,
        249 => self::NORMAL,
        275 => self::INVERSE,
        301 => self::NORMAL,
        327 => self::NORMAL,
        354 => self::INVERSE,
        380 => self::INVERSE,
        406 => self::INVERSE,
        432 => self::NORMAL,
        457 => self::INVERSE,
    ];

    const AR_SPONT_REPONSES = [
        228 => self::INVERSE,
        254 => self::NORMAL,
        280 => self::INVERSE,
        306 => self::NORMAL,
        332 => self::INVERSE,
        359 => self::NORMAL,
        385 => self::INVERSE,
        411 => self::NORMAL,
        437 => self::NORMAL,
        462 => self::NORMAL,
    ];

    const AR_NOUV_REPONSES = [
        233 => self::NORMAL,
        259 => self::INVERSE,
        285 => self::NORMAL,
        311 => self::INVERSE,
        337 => self::NORMAL,
        364 => self::INVERSE,
        390 => self::NORMAL,
        416 => self::NORMAL,
        442 => self::NORMAL,
        467 => self::NORMAL,
    ];

    const AR_DYN_REPONSES = [
        213 => self::NORMAL,
        239 => self::NORMAL,
        265 => self::NORMAL,
        291 => self::NORMAL,
        317 => self::NORMAL,
        343 => self::NORMAL,
        370 => self::NORMAL,
        396 => self::INVERSE,
        422 => self::NORMAL,
        447 => self::INVERSE,
    ];

    const PM_LEAD_REPONSES = [
        212 => self::NORMAL,
        238 => self::NORMAL,
        264 => self::NORMAL,
        290 => self::INVERSE,
        316 => self::NORMAL,
        342 => self::INVERSE,
        369 => self::NORMAL,
        395 => self::INVERSE,
        421 => self::INVERSE,
        446 => self::NORMAL,
    ];


    const PM_AFFIRM_REPONSES = [
        232 => self::NORMAL,
        258 => self::INVERSE,
        284 => self::INVERSE,
        310 => self::INVERSE,
        336 => self::NORMAL,
        363 => self::INVERSE,
        389 => self::INVERSE,
        415 => self::INVERSE,
        441 => self::INVERSE,
        466 => self::INVERSE,
    ];

    const PM_EMP_REPONSES = [
        217 => self::NORMAL,
        243 => self::INVERSE,
        269 => self::NORMAL,
        295 => self::INVERSE,
        321 => self::NORMAL,
        347 => self::INVERSE,
        374 => self::NORMAL,
        400 => self::INVERSE,
        426 => self::NORMAL,
        451 => self::INVERSE,
    ];

    const PM_ING_REPONSES = [
        222 => self::NORMAL,
        248 => self::NORMAL,
        274 => self::NORMAL,
        300 => self::NORMAL,
        326 => self::NORMAL,
        352 => self::NORMAL,
        379 => self::NORMAL,
        405 => self::NORMAL,
        431 => self::NORMAL,
        456 => self::INVERSE,
    ];

    const PM_SINTEL_REPONSES = [
        227 => self::NORMAL,
        253 => self::NORMAL,
        279 => self::NORMAL,
        305 => self::NORMAL,
        331 => self::NORMAL,
        358 => self::NORMAL,
        384 => self::NORMAL,
        410 => self::NORMAL,
        436 => self::NORMAL,
        461 => self::NORMAL,
    ];

    const ALL_FP_REPONSES =
        self::FP_ANX_REPONSES +
        self::FP_INTROS_REPONSES +
        self::FP_HDEP_REPONSES +
        self::FP_DEV_REPONSES +
        self::FP_GEN_REPONSES;

    const ALL_ME_REPONSES =
        self::ME_CE_REPONSES +
        self::ME_MODES_REPONSES +
        self::ME_COOPE_REPONSES +
        self::ME_AMB_REPONSES +
        self::ME_DROIT_REPONSES;

    const ALL_CP_REPONSES =
        self::CP_FIAB_REPONSES +
        self::CP_AUTODISC_REPONSES +
        self::CP_REFL_REPONSES +
        self::CP_RIG_REPONSES +
        self::CP_SVA_REPONSES;

    const ALL_AR_REPONSES =
        self::AR_CONF_REPONSES +
        self::AR_GREGA_REPONSES +
        self::AR_SPONT_REPONSES +
        self::AR_NOUV_REPONSES +
        self::AR_DYN_REPONSES;

    const ALL_PM_PERSONNALITE =
        self::PM_LEAD_REPONSES +
        self::PM_AFFIRM_REPONSES +
        self::PM_EMP_REPONSES +
        self::PM_ING_REPONSES +
        self::PM_SINTEL_REPONSES;

    const ALL_PERSONNALITE_INDEX_TO_TYPE =
        self::ALL_FP_REPONSES +
        self::ALL_ME_REPONSES +
        self::ALL_CP_REPONSES +
        self::ALL_AR_REPONSES +
        self::ALL_PM_PERSONNALITE;
}








