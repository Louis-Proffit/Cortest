<?php

namespace App\Fixture\Bmpr;

use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\Profil;
use App\Repository\ConcoursRepository;
use App\Repository\ProfilRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BpmrOffCorrecteurFixture extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function __construct(
        private readonly ProfilRepository   $profil_repository,
        private readonly ConcoursRepository $concours_repository,
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
        $concours = $this->concours_repository->findOneBy(["nom" => BpmrOffFixture::CONCOURS_NOM]);
        $profil = $this->profil_repository->findOneBy(["nom" => BpmrOffFixture::PROFIL_NOM]);

        // -------------------- correcteurs

        $correcteur = new Correcteur(
            0,
            $concours,
            $profil,
            self::CORRECTEUR_NOM,
            new ArrayCollection()
        );

        $this->echelleAptitudeCognitive($profil, $correcteur, self::VRAI_NOM_PHP_TO_INDEX_VRAI, "vrai");
        $this->echelleAptitudeCognitive($profil, $correcteur, self::FAUX_NOM_PHP_TO_INDEX_VRAI, "faux");
        $this->eg($profil, $correcteur);
        $this->qr($profil, $correcteur);

        $this->echellesPersonnalite($profil, $correcteur, self::NOM_PHP_COMPOSITE_TO_NOM_PHP_SIMPLE_TO_INDEX_TO_TYPE);
        $this->at($profil, $correcteur);
        $this->ds($profil, $correcteur);
        $this->rc($profil, $correcteur);

        $manager->persist($correcteur);
        $manager->flush();
    }

    private function eg(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $echelle = $this->findEchelleInProfil($profil, BpmrOffFixture::EG);
        $expression = "((" . $this->nombreBonnesReponsesCognitif() . ")*(" . $this->nombreReponsesTraiteesCognitif() . ")) ** 0.5";

        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    private function qr(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $echelle = $this->findEchelleInProfil($profil, BpmrOffFixture::QR);
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

        foreach (BpmrOffFixture::APTITUDES_COGNITIVES_ALL_BR as $echelle) {
            $nombre_bonnes_reponses = $nombre_bonnes_reponses . "+echelle(\"" . $echelle . "\")";
        }

        return $nombre_bonnes_reponses;
    }

    private function at(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $correcteur->echelles->add($this->echelleFromIndexToType(
            $correcteur,
            $this->findEchelleInProfil($profil, BpmrOffFixture::AT),
            self::AT_REPONSES
        ));
    }

    private function ds(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $correcteur->echelles->add($this->echelleFromIndexToType(
            $correcteur,
            $this->findEchelleInProfil($profil, BpmrOffFixture::DS),
            self::DS_REPONSES
        ));
    }

    private function rc(
        Profil     $profil,
        Correcteur $correcteur
    )
    {
        $expression = "0";
        foreach (self::ALL_PERSONNALITE_INDEX_TO_TYPE as $index => $type) {
            $expression = $expression . "+vraiC(" . $index . ")";
        }

        $echelle = $this->findEchelleInProfil($profil, BpmrOffFixture::RC);
        $correcteur->echelles->add(new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        ));
    }

    private function echellesPersonnalite(Profil $profil, Correcteur $correcteur, array $nom_php_composite_to_nom_php_simple_to_index_to_type)
    {

        foreach ($nom_php_composite_to_nom_php_simple_to_index_to_type as $nom_php_composite => $nom_php_simple_to_index_to_type) {

            $expression_composite = "0";
            $echelle_composite = $this->findEchelleInProfil($profil, $nom_php_composite);

            foreach ($nom_php_simple_to_index_to_type as $nom_php_simple => $index_to_type) {

                $echelle_simple = $this->findEchelleInProfil($profil, $nom_php_simple);

                $expression_composite = $expression_composite . "+echelle(\"" . $nom_php_simple . "\")";

                $correcteur->echelles->add($this->echelleFromIndexToType($correcteur, $echelle_simple, $index_to_type));
            }

            $correcteur->echelles->add(new EchelleCorrecteur(
                0,
                $expression_composite,
                $echelle_composite,
                $correcteur
            ));
        }
    }

    private function echelleFromIndexToType(Correcteur $correcteur, Echelle $echelle, array $index_to_type): EchelleCorrecteur
    {
        $expression = "0";
        foreach ($index_to_type as $index => $type) {
            $expression = $expression . "+" . $type . "(" . $index . ")";
        }

        return new EchelleCorrecteur(
            0,
            $expression,
            $echelle,
            $correcteur
        );
    }

    private function echelleAptitudeCognitive(Profil $profil, Correcteur $correcteur, array $nom_php_to_index_to_vrai, $base)
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

    private function findEchelleInProfil(Profil $profil, string $nom_php): Echelle|null
    {
        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            if ($echelle->nom_php === $nom_php) {
                return $echelle;
            }
        }
        return null;
    }

    const CORRECTEUR_NOM = "Correcteur par défaut";

    const A = "A";
    const B = "B";
    const C = "C";
    const D = "D";
    const E = "E";

    const VRAI_NOM_PHP_TO_INDEX_VRAI = [
        BpmrOffFixture::VT_BR => self::REPONSES_VT_INDEX_TO_VRAI,
        BpmrOffFixture::SP_BR => self::REPONSES_SP_INDEX_TO_VRAI,
        BpmrOffFixture::RAIS_BR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        BpmrOffFixture::CV_BR => self::REPONSES_CV_INDEX_TO_VRAI,
        BpmrOffFixture::SL_BR => self::REPONSES_SL_INDEX_TO_VRAI,
        BpmrOffFixture::DIC_BR => self::REPONSES_DIC_INDEX_TO_VRAI,
        BpmrOffFixture::AS_BR => self::REPONSES_AS_INDEX_TO_VRAI,
    ];

    const FAUX_NOM_PHP_TO_INDEX_VRAI = [
        BpmrOffFixture::VT_MR => self::REPONSES_VT_INDEX_TO_VRAI,
        BpmrOffFixture::SP_MR => self::REPONSES_SP_INDEX_TO_VRAI,
        BpmrOffFixture::RAIS_MR => self::REPONSES_RAIS_INDEX_TO_VRAI,
        BpmrOffFixture::CV_MR => self::REPONSES_CV_INDEX_TO_VRAI,
        BpmrOffFixture::SL_MR => self::REPONSES_SL_INDEX_TO_VRAI,
        BpmrOffFixture::DIC_MR => self::REPONSES_DIC_INDEX_TO_VRAI,
        BpmrOffFixture::AS_MR => self::REPONSES_AS_INDEX_TO_VRAI,
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
        89 => self::D,
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
        BpmrOffFixture::FP => [
            BpmrOffFixture::FP_ANX => self::FP_ANX_REPONSES,
            BpmrOffFixture::FP_INTROS => self::FP_INTROS_REPONSES,
            BpmrOffFixture::FP_HDEP => self::FP_HDEP_REPONSES,
            BpmrOffFixture::FP_DEV => self::FP_DEV_REPONSES,
            BpmrOffFixture::FP_GEN => self::FP_GEN_REPONSES,
        ],
        BpmrOffFixture::ME => [
            BpmrOffFixture::ME_CE => self::ME_CE_REPONSES,
            BpmrOffFixture::ME_HU => self::ME_HU_REPONSES,
            BpmrOffFixture::ME_COOPE => self::ME_COOPE_REPONSES,
            BpmrOffFixture::ME_AMB => self::ME_AMB_REPONSES,
            BpmrOffFixture::ME_DROIT => self::ME_DROIT_REPONSES,
        ],
        BpmrOffFixture::CP => [
            BpmrOffFixture::CP_FIAB => self::CP_FIAB_REPONSES,
            BpmrOffFixture::CP_AUTODISC => self::CP_AUTODISC_REPONSES,
            BpmrOffFixture::CP_REFL => self::CP_REFL_REPONSES,
            BpmrOffFixture::CP_RIG => self::CP_RIG_REPONSES,
            BpmrOffFixture::CP_SVA => self::CP_SVA_REPONSES,
        ],
        BpmrOffFixture::AR => [
            BpmrOffFixture::AR_CONF => self::AR_CONF_REPONSES,
            BpmrOffFixture::AR_GREGA => self::AR_GREGA_REPONSES,
            BpmrOffFixture::AR_SPONT => self::AR_SPONT_REPONSES,
            BpmrOffFixture::AR_NOUV => self::AR_NOUV_REPONSES,
            BpmrOffFixture::AR_DYN => self::AR_DYN_REPONSES,
        ],
        BpmrOffFixture::PM => [
            BpmrOffFixture::PM_LEAD => self::PM_LEAD_REPONSES,
            BpmrOffFixture::PM_AFFIRM => self::PM_AFFIRM_REPONSES,
            BpmrOffFixture::PM_EMP => self::PM_EMP_REPONSES,
            BpmrOffFixture::PM_ING => self::PM_ING_REPONSES,
            BpmrOffFixture::PM_SINTEL => self::PM_SINTEL_REPONSES,
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

    const ME_HU_REPONSES = [
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
        self::ME_HU_REPONSES +
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
    const ALL_PERSONNALITE_INDEX_TO_TYPE = self::ALL_FP_REPONSES + self::ALL_ME_REPONSES + self::ALL_CP_REPONSES + self::ALL_AR_REPONSES + self::ALL_PM_PERSONNALITE;
}



