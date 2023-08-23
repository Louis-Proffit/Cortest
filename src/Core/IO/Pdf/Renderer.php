<?php

namespace App\Core\IO\Pdf;

use App\Core\Exception\MissingFileException;
use App\Core\IO\GraphiqueFileRepository;
use App\Core\ScoreBrut\ScoreBrut;
use App\Core\ScoreEtalonne\ScoreEtalonne;
use App\Core\ScoreEtalonne\ScoresEtalonnes;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Test;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class Renderer
{
    const KEY_IMAGE_DIRECTORY = "dossier_images";
    const KEY_REPONSE_NOM = "reponse_nom";
    const KEY_REPONSE_PRENOM = "reponse_prenom";
    const KEY_REPONSE_AUTRE_1 = "reponse_autre_1";
    const KEY_REPONSE_AUTRE_2 = "reponse_autre_2";
    const KEY_REPONSE_CODE_BARRE = "reponse_code_barre";
    const KEY_REPONSE_DATE_DE_NAISSANCE = "reponse_date_de_naissance";
    const KEY_REPONSE_EIRS = "reponse_eirs";
    const KEY_REPONSE_NIVEAU_SCOLAIRE_NOM = "reponse_niveau_scolaire_nom";
    const KEY_REPONSE_NIVEAU_SCOLAIRE_INDICE = "reponse_niveau_scolaire_indice";
    const KEY_REPONSE_NOM_JEUNE_FILLE = "reponse_nom_jeune_fille";
    const KEY_REPONSE_RESERVE = "reponse_reserve";
    const KEY_REPONSE_SEXE = "reponse_sexe";
    const KEY_SESSION_DATE = "session_date";
    const KEY_SESSION_NUMERO_ORDRE = "session_numero_ordre";
    const KEY_SESSION_SGAP_NOM = "session_sgap_nom";
    const KEY_CONCOURS_VERSION_BATTERIE = "concours_version_batterie";
    const KEY_ETALONNAGE_NOMBRE_CLASSES = "etalonnage_nombre_classes";
    const KEY_PREFIX_SCORE = "score_";
    const KEY_PREFIX_PROFIL = "profil_";
    const KEY_PREFIX_ETALONNAGE = "etalonnage_borne_";
    const KEY_GRAPHIQUE_NOM = "graphique_nom";
    const KEY_PROFIL_NOM = "profil_nom";
    const KEY_PREFIX_NOM = "nom_";

    private string $imagesDirectory;


    public function __construct(
        private readonly GraphiqueFileRepository $graphiqueFileManager,
        private readonly Environment             $environment,
        private readonly string                  $separator = "/"
    )
    {
        $this->imagesDirectory = str_replace(DIRECTORY_SEPARATOR, $this->separator, getCwd() . DIRECTORY_SEPARATOR . "renderer" . DIRECTORY_SEPARATOR);
    }

    public
    function optionKeys(
        Test       $test,
        Correcteur $correcteur,
        Etalonnage $etalonnage,
    ): array
    {
        $profil = $correcteur->structure;
        $graphique = new Graphique(0, $profil, "TEST", "TEST");
        $reponse = $this->dummyReponse($test);

        $scoreBrut = new ScoreBrut();
        $scoreEtalonne = new ScoreEtalonne();

        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            $scoreBrut->set($echelle, 1);
            $scoreEtalonne->set($echelle, 1);
        }

        return array_keys($this->rawOptionsArray(
            graphique: $graphique,
            reponse: $reponse,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scoreBrut: $scoreBrut,
            scoreEtalonne: $scoreEtalonne
        ));
    }

    public
    function rawOptionsArray(
        Graphique       $graphique,
        ReponseCandidat $reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        ScoreBrut       $scoreBrut,
        ScoreEtalonne   $scoreEtalonne,
    ): array
    {
        $result = [
            self::KEY_REPONSE_NOM => $reponse->nom,
            self::KEY_REPONSE_PRENOM => $reponse->prenom,
            self::KEY_REPONSE_AUTRE_1 => $reponse->autre_1,
            self::KEY_REPONSE_AUTRE_2 => $reponse->autre_2,
            self::KEY_REPONSE_CODE_BARRE => $reponse->code_barre,
            self::KEY_REPONSE_DATE_DE_NAISSANCE => $reponse->date_de_naissance,
            self::KEY_REPONSE_EIRS => $reponse->eirs,
            self::KEY_REPONSE_NIVEAU_SCOLAIRE_NOM => $reponse->niveau_scolaire->nom,
            self::KEY_REPONSE_NIVEAU_SCOLAIRE_INDICE => $reponse->niveau_scolaire->indice,
            self::KEY_REPONSE_NOM_JEUNE_FILLE => $reponse->nom_jeune_fille,
            self::KEY_REPONSE_RESERVE => $reponse->reserve,
            self::KEY_REPONSE_SEXE => $reponse->sexe,
            self::KEY_SESSION_DATE => $reponse->session->date,
            self::KEY_SESSION_NUMERO_ORDRE => $reponse->session->numero_ordre,
            self::KEY_SESSION_SGAP_NOM => $reponse->session->sgap->nom,
            self::KEY_CONCOURS_VERSION_BATTERIE => $reponse->session->test->version_batterie,
            self::KEY_IMAGE_DIRECTORY => $this->imagesDirectory,
            self::KEY_GRAPHIQUE_NOM => $graphique->nom,
            self::KEY_PROFIL_NOM => $correcteur->structure->nom,
            self::KEY_ETALONNAGE_NOMBRE_CLASSES => $etalonnage->nombre_classes
        ];

        foreach ($scoreBrut->getAll() as $echelle => $scoreValue) {
            $result[self::KEY_PREFIX_SCORE . $echelle] = $scoreValue;
        }

        foreach ($scoreEtalonne->getAll() as $echelle => $profilValue) {
            $result[self::KEY_PREFIX_PROFIL . $echelle] = $profilValue;
        }

        foreach ($correcteur->get_echelle_noms() as $echelle_php => $echelle_nom) {
            $result[self::KEY_PREFIX_NOM . $echelle_php] = $echelle_nom;
        }

        /** @var EchelleEtalonnage $echelleEtalonnage */
        foreach ($etalonnage->echelles as $echelleEtalonnage) {
            $index = 0;
            foreach ($echelleEtalonnage->bounds as $bound) {
                $result[self::KEY_PREFIX_ETALONNAGE . $echelleEtalonnage->echelle->nom_php . "_" . $index] = $bound;
                $index++;
            }
        }

        return $result;
    }

    /**
     * @throws SyntaxError
     * @throws LoaderError
     * @throws MissingFileException
     */
    public
    function getFeuilleProfilContent(
        Graphique       $graphique,
        ReponseCandidat $reponseCandidat,
        Etalonnage      $etalonnage,
        Correcteur      $correcteur,
        ScoreBrut       $scoreBrut,
        ScoreEtalonne   $scoreEtalonne,
    ): string
    {
        $templateContent = $this->graphiqueFileManager->entityFileContent($graphique);
        $template = $this->environment->createTemplate($templateContent);

        return $template->render(context: $this->rawOptionsArray(
            graphique: $graphique,
            reponse: $reponseCandidat,
            correcteur: $correcteur,
            etalonnage: $etalonnage,
            scoreBrut: $scoreBrut,
            scoreEtalonne: $scoreEtalonne
        ));
    }

    public
    function dummySession(Test $test): Session
    {
        $sgap = new Sgap(id: 0, indice: 1, nom: "SGAP TEST", sessions: new ArrayCollection());
        $session = new Session(
            id: 0,
            date: new DateTime(),
            numero_ordre: 1,
            observations: "OBSERVATIONS TEST",
            test: $test,
            sgap: $sgap,
            reponses_candidats: new ArrayCollection()
        );
        $sgap->sessions->add($session);
        return $session;
    }

    public
    function dummyReponse(Test $test): ReponseCandidat
    {
        $reponses = [];

        /** @var QuestionTest $question */
        foreach ($test->questions as $question) {
            $reponses[$question->indice] = 3;
        }

        return new ReponseCandidat(
            id: 0,
            session: $this->dummySession($test),
            reponses: $reponses,
            nom: "NOM TEST",
            prenom: "PRENOM TEST",
            nom_jeune_fille: "NJF TEST",
            niveau_scolaire: new NiveauScolaire(id: 0, indice: 1, nom: "TEST"),
            date_de_naissance: new DateTime(),
            sexe: 0,
            reserve: "RESERVE TEST",
            autre_1: "AUTRE 1 TEST",
            autre_2: "AUTRE 2 TEST",
            code_barre: 0,
            eirs: "E",
            raw: null
        );
    }

}