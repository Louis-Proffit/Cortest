<?php

namespace App\Fixture\Bpmr;

use App\Core\Renderer\Values\RendererBatonnets;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\Graphique;
use App\Entity\Profil;
use App\Entity\QuestionConcours;
use App\Entity\Subtest;
use Doctrine\Common\Collections\ArrayCollection;

class BpmrOffFixture extends AbstractBpmrFixture
{

    public function __construct()
    {
        parent::__construct(
            "44",
            "440",
            456,
            self::CONCOURS_NOM,
            self::PROFIL_NOM,
            self::CORRECTEUR_NOM,
            self::ETALONNAGE_NOM,
            9,
            self::GRAPHIQUE_NOM
        );
    }

    protected function aptitudesCognitives(Profil $profil): void
    {
        $this->echellesSimplesAptitudesCognitives($profil, self::APTITUDES_COGNITIVES_NOM_PHP_TO_NOM);

        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "EG",
            nom_php: self::EG,
            type: Echelle::TYPE_ECHELLE_COMPOSITE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));
        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "QR",
            nom_php: self::QR,
            type: Echelle::TYPE_ECHELLE_COMPOSITE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));
    }

    protected function personnalite(Profil $profil): void
    {
        $this->echellesSimplesEtCompositesPersonnalite(
            $profil,
            self::PERSONNALITE_NOM_PHP_TO_NOM_SIMPLE,
            self::PERSONNALITE_NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE
        );

        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "DS",
            nom_php: self::DS,
            type: Echelle::TYPE_ECHELLE_SIMPLE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));

        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "AT",
            nom_php: self::AT,
            type: Echelle::TYPE_ECHELLE_SIMPLE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));

        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "RC",
            nom_php: self::RC,
            type: Echelle::TYPE_ECHELLE_SIMPLE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));

        $profil->echelles->add(new Echelle(
            id: 0,
            nom: "RCPOURCENT",
            nom_php: self::RCPOURCENT,
            type: Echelle::TYPE_ECHELLE_SIMPLE,
            echelles_correcteur: new ArrayCollection(),
            echelles_etalonnage: new ArrayCollection(),
            echelles_graphiques: new ArrayCollection(),
            profil: $profil
        ));
    }

    protected function questions(Concours $concours): void
    {
        $this->questionsTypeIndexAsValue($concours, self::INDEX_EXEMPLES, QuestionConcours::TYPE_EXEMPLE);
        $this->questionsTypeIndexAsKey(concours: $concours,
            index_to_any: self::ALL_APTITUDES_COGNITIVES,
            type: QuestionConcours::TYPE_VRAI_FAUX);
        $this->questionsTypeIndexAsKey($concours,
            self::ALL_PERSONNALITE_INDEX_TO_TYPE,
            QuestionConcours::TYPE_SCORE);
    }

    private function correcteurEg(
        Profil     $profil,
        Correcteur $correcteur
    ): void
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
    ): void
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
    ): void
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
    ): void
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
    ): void
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

    private function correcteurRcPourcent(
        Profil     $profil,
        Correcteur $correcteur
    ): void
    {
        $expression = "echelle(\"" . self::RC . "\")*100/" . count(self::ALL_PERSONNALITE_INDEX_TO_TYPE);

        $echelle = $this->findEchelleInProfil($profil, self::RCPOURCENT);
        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    protected function correcteurAptitudesCognitives(Profil $profil, Correcteur $correcteur): void
    {
        $this->echellesCorrecteurAptitudeCognitive($profil, $correcteur, self::VRAI_NOM_PHP_TO_INDEX_VRAI, "vrai");
        $this->echellesCorrecteurAptitudeCognitive($profil, $correcteur, self::FAUX_NOM_PHP_TO_INDEX_VRAI, "faux");
        $this->correcteurEg($profil, $correcteur);
        $this->correcteurQr($profil, $correcteur);
    }

    protected function correcteurPersonnalite(Profil $profil, Correcteur $correcteur): void
    {
        $this->echellesCorrecteurPersonnalite($profil,
            $correcteur,
            self::NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE_TO_INDEX_TO_TYPE);
        $this->correcteurAt($profil, $correcteur);
        $this->correcteurDs($profil, $correcteur);
        $this->correcteurRc($profil, $correcteur);
        $this->correcteurRcPourcent($profil, $correcteur);
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
            array($this->findEchelleInGraphique($graphique, self::RCPOURCENT)->id, Subtest::TYPE_FOOTER_POURCENT),
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


    protected function subtests(Graphique $graphique): void
    {
        $graphique->options[RendererBatonnets::OPTION_TITRE_PHP] = "PROFIL BPMR-OFF";

        $graphique->subtests->add($this->subtestAptitudesCognitives($graphique));
        $graphique->subtests->add($this->subtestPersonnalite($graphique));
    }


    const CONCOURS_NOM = "Concours BPMR - Officier";
    const PROFIL_NOM = "BPMR - Officier";
    const CORRECTEUR_NOM = "Correcteur par défaut BPMR - OFF";
    const ETALONNAGE_NOM = "Etalonnage de test BPMR - OFF";
    const GRAPHIQUE_NOM = "Graphique par défaut BPMR - OFF";

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

    const APTITUDES_COGNITIVES_NOM_PHP_TO_NOM = [
        self::VT_BR => "VT",
        self::SP_BR => "SP",
        self::RAIS_BR => "Rais",
        self::CV_BR => "CV",
        self::SL_BR => "SL",
        self::DIC_BR => "DIC",
        self::AS_BR => "AS",
        self::VT_MR => "VT Mauvaises réponses",
        self::SP_MR => "SP Mauvaises réponses",
        self::RAIS_MR => "Rais Mauvaises réponses",
        self::CV_MR => "CV Mauvaises réponses",
        self::SL_MR => "SL Mauvaises réponses",
        self::DIC_MR => "DIC Mauvaises réponses",
        self::AS_MR => "AS Mauvaises réponses",
    ];

    const APTITUDES_COGNITIVES_NOM_PHP_BR = [
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
    const RCPOURCENT = "rc_pourcent";

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
        self::VT_BR => self::REPONSES_VT_INDEX_TO_VRAI,
        self::SP_BR => self::REPONSES_SP_INDEX_TO_VRAI,
        self::RAIS_BR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        self::CV_BR => self::REPONSES_CV_INDEX_TO_VRAI,
        self::SL_BR => self::REPONSES_SL_INDEX_TO_VRAI,
        self::DIC_BR => self::REPONSES_DIC_INDEX_TO_VRAI,
        self::AS_BR => self::REPONSES_AS_INDEX_TO_VRAI,
    ];

    const FAUX_NOM_PHP_TO_INDEX_VRAI = [
        self::VT_MR => self::REPONSES_VT_INDEX_TO_VRAI,
        self::SP_MR => self::REPONSES_SP_INDEX_TO_VRAI,
        self::RAIS_MR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        self::CV_MR => self::REPONSES_CV_INDEX_TO_VRAI,
        self::SL_MR => self::REPONSES_SL_INDEX_TO_VRAI,
        self::DIC_MR => self::REPONSES_DIC_INDEX_TO_VRAI,
        self::AS_MR => self::REPONSES_AS_INDEX_TO_VRAI,
    ];


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
        34 => self::D,
        35 => self::A,
        36 => self::A,
        37 => self::B,
        38 => self::C,
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
        89 => self::A,
        90 => self::B,
        91 => self::C,
    ];


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

    const ALL_APTITUDES_COGNITIVES = self::REPONSES_VT_INDEX_TO_VRAI +
    self::REPONSES_SP_INDEX_TO_VRAI +
    self::REPONSES_RAIS_INDEX_TO_VRAI +
    self::REPONSES_CV_INDEX_TO_VRAI +
    self::REPONSES_SL_INDEX_TO_VRAI +
    self::REPONSES_DIC_INDEX_TO_VRAI +
    self::REPONSES_AS_INDEX_TO_VRAI;

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
        196 => self::NORMAL,
        340 => self::NORMAL,
        456 => self::NORMAL
    ];

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


    const FP_INTROS_REPONSES = [
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

    const FP_GEN_REPONSES = [
        215 => self::NORMAL,
        241 => self::NORMAL,
        267 => self::NORMAL,
        293 => self::NORMAL,
        318 => self::NORMAL,
        345 => self::NORMAL,
        371 => self::NORMAL,
        397 => self::NORMAL,
        422 => self::NORMAL,
        448 => self::NORMAL,
    ];

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

    const ME_AMB_REPONSES = [
        214 => self::NORMAL,
        240 => self::NORMAL,
        266 => self::NORMAL,
        292 => self::NORMAL,
        317 => self::NORMAL,
        344 => self::NORMAL,
        370 => self::NORMAL,
        396 => self::NORMAL,
        421 => self::NORMAL,
        447 => self::NORMAL,
    ];

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

    const PM_LEAD_REPONSES = [
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
        self::ALL_PM_PERSONNALITE +
        self::AT_REPONSES +
        self::DS_REPONSES;
}








