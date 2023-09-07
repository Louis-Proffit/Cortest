<?php

namespace App\Tests;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\CortestUser;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\ReponseCandidat;
use App\Entity\Resource;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Structure;
use App\Entity\Test;
use App\Repository\GrilleRepository;
use App\Tests\CortestTestTrait;
use App\Tests\DoctrineTestTrait;
use App\Tests\LoginTestTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SmokeTestCase extends WebTestCase
{
    use CortestTestTrait;
    use DoctrineTestTrait;
    use LoginTestTrait;

    private static int $concoursId;
    private static int $testId;
    private static int $sgapId;
    private static int $sessionId;
    private static int $reponseCandidatId;
    private static int $niveauScolaireId;
    private static int $administrateurId;
    private static int $structureId;
    private static int $correcteurId;
    private static int $etalonnageId;
    private static int $graphiqueId;
    private static int $resourceId;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->initClient();
        $this->login(CortestUser::ROLE_ADMINISTRATEUR);

        $entityManager = $this->getEntityManager();
        $sgap = new Sgap(
            id: 0,
            indice: 1,
            nom: "sgap",
            sessions: new ArrayCollection()
        );
        $concours = new Concours(
            id: 1,
            nom: "concours",
            type_concours: 1,
            tests: new ArrayCollection(),
            sessions: new ArrayCollection()
        );
        $administrateur = new CortestUser(
            id: 1,
            username: "username",
            password: "password",
            role: CortestUser::ROLE_ADMINISTRATEUR
        );
        $test = new Test(
            id: 1,
            nom: "test",
            version_batterie: 1,
            index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            concours: new ArrayCollection([$concours]),
            correcteurs: new ArrayCollection(),
            sessions: new ArrayCollection(),
            questions: new ArrayCollection()
        );
        $session = new Session(
            id: 0,
            date: new DateTime(),
            numero_ordre: 0,
            observations: "observations",
            test: $test,
            sgap: $sgap,
            reponses_candidats: new ArrayCollection(),
            concours: $concours
        );
        $niveauScolaire = new NiveauScolaire(
            id: 0,
            indice: 1,
            nom: "niveau_scolaire"
        );

        $reponseCandidat = new ReponseCandidat(
            id: 0,
            session: $session,
            reponses: array_fill(1, 640, 2),
            nom: "nom",
            prenom: "prenom",
            nom_jeune_fille: "nom_jeune_fille",
            niveau_scolaire: $niveauScolaire,
            date_de_naissance: new DateTime(),
            sexe: ReponseCandidat::INDEX_FEMME,
            reserve: "reserve",
            autre_1: "autre_1",
            autre_2: "autre_2",
            code_barre: 0,
            eirs: ReponseCandidat::TYPE_E, raw: null
        );

        $session->reponses_candidats->add($reponseCandidat);

        $structure = new Structure(
            id: 0,
            nom: "structure",
            echelles: new ArrayCollection(),
            correcteurs: new ArrayCollection(),
            etalonnages: new ArrayCollection(),
            graphiques: new ArrayCollection()
        );

        $correcteur = new Correcteur(
            id: 0,
            tests: new ArrayCollection([$test]),
            structure: $structure,
            nom: "correcteur",
            echelles: new ArrayCollection()
        );
        $structure->correcteurs->add($correcteur);
        $etalonnage = new Etalonnage(
            id: 0,
            structure: $structure,
            nom: "etalonnage",
            nombre_classes: 9,
            echelles: new ArrayCollection()
        );
        $structure->etalonnages->add($etalonnage);

        for ($i = 0; $i < 10; $i++) {
            $echelle = new Echelle(
                id: 0,
                nom: "nom" . $i,
                nom_php: "nom_php" . $i,
                type: Echelle::TYPE_ECHELLE_SIMPLE,
                echelles_correcteur: new ArrayCollection(),
                echelles_etalonnage: new ArrayCollection(),
                structure: $structure
            );
            $structure->echelles->add($echelle);
            $correcteur->echelles->add(new EchelleCorrecteur(
                id: 0,
                expression: "$i",
                echelle: $echelle,
                correcteur: $correcteur
            ));
            $etalonnage->echelles->add(new EchelleEtalonnage(
                id: 0,
                bounds: range(1, 9),
                echelle: $echelle,
                etalonnage: $etalonnage
            ));
        }

        $graphique = new Graphique(
            id: 0,
            structure: $structure,
            nom: "graphique",
            file_nom: "file"
        );

        $structure->graphiques->add($graphique);

        $resource = new Resource(
            id: 0,
            nom: "nom",
            file_nom: "file",
        );

        $entityManager->persist($sgap);
        $entityManager->persist($administrateur);
        $entityManager->persist($test);
        $entityManager->persist($concours);
        $entityManager->persist($niveauScolaire);
        $entityManager->persist($session);
        $entityManager->persist($structure);
        $entityManager->persist($correcteur);
        $entityManager->persist($etalonnage);
        $entityManager->persist($resource);
        $entityManager->persist($graphique);

        self::$testId = $test->id;
        self::$concoursId = $concours->id;
        self::$administrateurId = $administrateur->id;
        self::$sgapId = $sgap->id;
        self::$niveauScolaireId = $niveauScolaire->id;
        self::$sessionId = $session->id;
        self::$structureId = $structure->id;
        self::$correcteurId = $correcteur->id;
        self::$etalonnageId = $etalonnage->id;
        self::$graphiqueId = $graphique->id;
        self::$resourceId = $resource->id;
        self::$reponseCandidatId = $reponseCandidat->id;

        $entityManager->flush();

        $this->client->request(Request::METHOD_GET, "/session/csv/" . self::$sessionId);
    }

    /**
     * @dataProvider provideUrls
     * @param string $url
     * @return void
     */
    public function testSmoke(string|callable $url, callable|string|null $redirect): void
    {
        if (is_callable($url)) {
            $url = $url();
        }
        $this->client->request(Request::METHOD_GET, $url);

        if ($redirect == null) {
            self::assertResponseIsSuccessful();
        } else {
            if (is_callable($redirect)) {
                $redirect = $redirect();
            }
            self::assertResponseRedirects($redirect);
        }
    }

    public function provideUrls(): Generator
    {
        yield "home" => [fn() => "/", null];

        yield "login" => [fn() => "/login", null];
        yield "logout" => [fn() => "/logout", "http://localhost/"];

        yield "grille_index" => [fn() => "/grille/index", null];

        yield "recherche_index" => [fn() => "/recherche/index", null];
        yield "recherche_vider" => [fn() => "/recherche/vider", "/recherche/index"];
        yield "recherche_deselectionner" => [fn() => "/recherche/deselectionner/" . self::$reponseCandidatId, "/recherche/index"];

        yield "lecture_index" => [fn() => "/lecture/index", null];
        yield "lecture_form" => [fn() => "/lecture/form", null];
        yield "lecture_scanner" => [fn() => "/lecture/scanner", null];
        # TODO add json response yield "lecture_scanner_save" => [fn() => "/lecture/scanner/save", null];
        yield "lecture_json" => [fn() => "/lecture/fichier-json", null];
        yield "lecture_csv" => [fn() => "/lecture/fichier-csv", null];
        yield "lecture_optique" => [fn() => "/lecture/optique/" . self::$sessionId, null];

        yield "session_index" => [fn() => "/session/index", null];
        yield "session_creer" => [fn() => "/session/creer", null];
        yield "session_modifier" => [fn() => "/session/modifier/" . self::$sessionId, null];
        yield "session_consulter" => [fn() => "/session/consulter/" . self::$sessionId, null];
        yield "session_csv" => [fn() => "/session/csv/" . self::$sessionId, "/csv/reponses"];
        yield "session_supprimer" => [fn() => "/session/supprimer/" . self::$sessionId, "/session/index"];

        yield "test_index" => [fn() => "/test/index", null];
        yield "test_creer" => [fn() => "/test/creer", null];
        yield "test_consulter" => [fn() => "/test/consulter/" . self::$testId, null];
        yield "test_modifier" => [fn() => "/test/modifier/" . self::$testId, null];
        yield "test_supprimer_confirmer" => [fn() => "/test/supprimer/confirmer/" . self::$testId, null];
        yield "test_supprimer" => [fn() => "/test/supprimer/" . self::$testId, fn() => "/test/supprimer/confirmer/" . self::$testId];

        yield "admin_index" => [fn() => "/admin/index", null];
        yield "admin_creer" => [fn() => "/admin/creer", null];
        yield "admin_modifier" => [fn() => "/admin/modifier/" . self::$administrateurId, null];
        yield "admin_modifier_mdp" => [fn() => "/admin/modifier-mdp/" . self::$administrateurId, null];
        yield "admin_supprimer" => [fn() => "/admin/supprimer/" . self::$administrateurId, "/admin/index"];

        yield "sgap_index" => [fn() => "/sgap/index", null];
        yield "sgap_creer" => [fn() => "/sgap/creer", null];
        yield "sgap_modifier" => [fn() => "/sgap/modifier/" . self::$sgapId, null];
        yield "sgap_supprimer_confirmer" => [fn() => "/sgap/supprimer/confirmer/" . self::$sgapId, null];
        yield "sgap_supprimer" => [fn() => "/sgap/supprimer/" . self::$sgapId, fn() => "/sgap/supprimer/confirmer/" . self::$sgapId];

        yield "structure_index" => [fn() => "/structure/index", null];
        yield "structure_creer" => [fn() => "/structure/creer", null];
        yield "structure_consulter" => [fn() => "/structure/consulter/" . self::$structureId, null];
        yield "structure_modifier" => [fn() => "/structure/modifier/" . self::$structureId, null];
        yield "structure_supprimer_confirmer" => [fn() => "/structure/supprimer/confirmer/" . self::$structureId, null];
        yield "structure_supprimer" => [fn() => "/structure/supprimer/" . self::$structureId, fn() => "/structure/supprimer/confirmer/" . self::$structureId];

        yield "niveau_scolaire_index" => [fn() => "/niveau-scolaire/index", null];
        yield "niveau_scolaire_creer" => [fn() => "/niveau-scolaire/creer", null];
        yield "niveau_scolaire_modifier" => [fn() => "/niveau-scolaire/modifier/" . self::$niveauScolaireId, null];
        yield "niveau_scolaire_supprimer_confirmer" => [fn() => "/niveau-scolaire/supprimer/confirmer/" . self::$niveauScolaireId, null];
        yield "niveau_scolaire_supprimer" => [fn() => "/niveau-scolaire/supprimer/" . self::$niveauScolaireId, fn() => "/niveau-scolaire/supprimer/confirmer/" . self::$niveauScolaireId];

        yield "concours_index" => [fn() => "/concours/index", null];
        yield "concours_creer" => [fn() => "/concours/creer", null];
        yield "concours_modifier" => [fn() => "/concours/modifier/" . self::$concoursId, null];
        yield "concours_supprimer_confirmer" => [fn() => "/concours/supprimer/confirmer/" . self::$concoursId, null];
        yield "concours_supprimer" => [fn() => "/concours/supprimer/" . self::$concoursId, fn() => "/concours/supprimer/confirmer/" . self::$concoursId];

        yield "correcteur_index" => [fn() => "/correcteur/index", null];
        yield "correcteur_consulter" => [fn() => "/correcteur/index", null];
        yield "correcteur_importer" => [fn() => "/correcteur/importer", null];
        yield "correcteur_modifier" => [fn() => "/correcteur/modifier/" . self::$correcteurId, null];
        yield "correcteur_exporter" => [fn() => "/correcteur/exporter/" . self::$correcteurId, null];
        yield "correcteur_supprimer" => [fn() => "/correcteur/supprimer/" . self::$correcteurId, "/correcteur/index"];

        yield "etalonnage_index" => [fn() => "/etalonnage/index", null];
        yield "etalonnage_creer" => [fn() => "/etalonnage/creer", null];
        yield "etalonnage_creer_simple" => [fn() => "/etalonnage/creer/simple", null];
        yield "etalonnage_creer_gaussien" => [fn() => "/etalonnage/creer/gaussien", null];
        yield "etalonnage_consulter" => [fn() => "/etalonnage/index", null];
        yield "etalonnage_modifier" => [fn() => "/etalonnage/modifier/" . self::$etalonnageId, null];
        yield "etalonnage_supprimer" => [fn() => "/etalonnage/supprimer/" . self::$etalonnageId, "/etalonnage/index"];

        yield "echelle_index" => [fn() => "/echelle/index", null];

        yield "graphique_index" => [fn() => "/graphique/index", null];
        yield "graphique_creer" => [fn() => "/graphique/creer", null];
        yield "graphique_modifier" => [fn() => "/graphique/modifier/" . self::$graphiqueId, null];
        yield "graphique_tester" => [fn() => "/graphique/tester/" . self::$graphiqueId, null];
        yield "graphique_telecharger" => [fn() => "/graphique/telecharger/" . self::$graphiqueId, null];
        yield "graphique_verifier_variables" => [fn() => "/graphique/verifier-variables", null];
        yield "graphique_supprimer" => [fn() => "/graphique/supprimer/" . self::$graphiqueId, "/graphique/index"];

        yield "csv_reponses" => [fn() => "/csv/reponses", null];
        yield "csv_scores_bruts" => [fn() => "/csv/scores-bruts/" . self::$correcteurId, null];
        yield "csv_scores_etalonnes" => [fn() => "/csv/scores-etalonnes/" . self::$correcteurId . "/" . self::$etalonnageId, null];

        yield "reponse_candidat_supprimer" => [fn() => "/reponse-candidat/supprimer/" . self::$reponseCandidatId, fn() => "/session/consulter/" . self::$sessionId];

        yield "resource_telecharger" => [fn() => "/resource/telecharger/" . self::$resourceId, null];
        yield "resource_creer" => [fn() => "/resource/creer", null];
        yield "resource_supprimer" => [fn() => "/resource/supprimer/" . self::$resourceId, "/"];

        yield "calcul_score_brut_form_session" => [fn() => "/calcul/score-brut/form/session/" . self::$sessionId, "/calcul/score-brut/form"];
        yield "calcul_score_brut_form" => [fn() => "/calcul/score-brut/form", null];
        yield "calcul_score_brut_index" => [fn() => "/calcul/score-brut/index/" . self::$correcteurId, null];

        yield "calcul_score_etalonne_form_session" => [fn() => "/calcul/score-etalonne/form/session/" . self::$sessionId, "/calcul/score-etalonne/form"];
        yield "calcul_score_etalonne_form" => [fn() => "/calcul/score-etalonne/form", null];
        yield "calcul_score_etalonne_form_score_session" => [fn() => "/calcul/score-etalonne/form/score-brut/session/" . self::$sessionId . "/" . self::$correcteurId, fn() => "/calcul/score-etalonne/form/score-brut/" . self::$correcteurId];
        yield "calcul_score_etalonne_form_score" => [fn() => "/calcul/score-etalonne/form/score-brut/" . self::$correcteurId, null];
        yield "calcul_score_etalonne_index" => [fn() => "/calcul/score-etalonne/index/" . self::$correcteurId . "/" . self::$etalonnageId, null];

        yield "pdf_form_simple" => [fn() => "/pdf/form/simple/" . self::$reponseCandidatId . "/" . self::$correcteurId . "/" . self::$etalonnageId, null];
        yield "pdf_form_zip" => [fn() => "/pdf/form/zip/" . self::$correcteurId . "/" . self::$etalonnageId, null];
        yield "pdf_form_merged" => [fn() => "/pdf/form/merged/" . self::$correcteurId . "/" . self::$etalonnageId, null];
        yield "pdf_telecharger_simple" => [fn() => "/pdf/telecharger/simple/" . self::$reponseCandidatId . "/" . self::$correcteurId . "/" . self::$etalonnageId . "/" . self::$graphiqueId, null];
        yield "pdf_telecharger_zip" => [fn() => "/pdf/telecharger/zip/" . self::$correcteurId . "/" . self::$etalonnageId . "/" . self::$graphiqueId, null];
        yield "pdf_telecharger_merged" => [fn() => "/pdf/telecharger/merged/" . self::$correcteurId . "/" . self::$etalonnageId . "/" . self::$graphiqueId, null];
    }
}