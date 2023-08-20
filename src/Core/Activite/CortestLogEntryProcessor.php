<?php

namespace App\Core\Activite;

use App\Core\ScoreBrut\ScoresBruts;
use App\Core\ScoreEtalonne\ScoresEtalonnes;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\CortestLogEntry;
use App\Entity\CortestUser;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Resource;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Structure;
use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class CortestLogEntryProcessor
{

    const CLASSES = [
        Concours::class,
        Correcteur::class,
        CortestUser::class,
        Echelle::class,
        EchelleCorrecteur::class,
        EchelleEtalonnage::class,
        Etalonnage::class,
        Graphique::class,
        NiveauScolaire::class,
        QuestionTest::class,
        ReponseCandidat::class,
        Resource::class,
        Session::class,
        Sgap::class,
        Structure::class,
        Test::class,
        ScoresBruts::class,
        ScoresEtalonnes::class,
        null
    ];

    const CLASS_NAMES = [
        Concours::class => "Concours",
        Correcteur::class => "Correcteur",
        CortestUser::class => "Utilisateur",
        Echelle::class => "Echelle",
        EchelleCorrecteur::class => "Echelle correcteur",
        EchelleEtalonnage::class => "Echelle etalonnage",
        Etalonnage::class => "Etalonnage",
        Graphique::class => "Graphique",
        NiveauScolaire::class => "Niveau scolaire",
        QuestionTest::class => "Question de test",
        ReponseCandidat::class => "Reponse de candidat",
        Resource::class => "Resource",
        Session::class => "Session",
        Sgap::class => "SGAP",
        Structure::class => "Structure",
        Test::class => "Test",
        ScoresBruts::class => "Scores bruts",
        ScoresEtalonnes::class => "Scores étalonnés",
        null => "Sans objet"
    ];

    const CLASS_INFOS = [
        Concours::class => "Concours, avec intitulé et type",
        Correcteur::class => "Transforme (\"corrige\") des réponses de candidat en scores bruts",
        CortestUser::class => "Utilisateur de CORTEST",
        Echelle::class => "Echelle (simple, composite...) d'une structure",
        Etalonnage::class => "Transforme (\"étalonne\") des scores bruts en scores étalonnés",
        Graphique::class => "Transforme des scores en une représentation graphique",
        NiveauScolaire::class => "-",
        QuestionTest::class => "Question dans un test",
        ReponseCandidat::class => "Données d'un candidat pour une session",
        Resource::class => "Resource partagée par les utilisateurs de CORTEST",
        Session::class => "Session d'examen",
        Sgap::class => "-",
        Structure::class => "Structure d'échelles, permet de former un profil",
        Test::class => "-",
        ScoresBruts::class => "-",
        ScoresEtalonnes::class => "-",
        null => "-"
    ];

    const ACTIONS = CortestLogEntry::ACTIONS;

    const ACTION_NAMES = [
        CortestLogEntry::ACTION_CREER => "Création",
        CortestLogEntry::ACTION_CALCULER => "Calcul",
        CortestLogEntry::ACTION_MODIFIER => "Modification",
        CortestLogEntry::ACTION_SUPPRIMER => "Suppression",
        CortestLogEntry::ACTION_EXPORTER => "Export",
        CortestLogEntry::ACTION_UTILISATEUR => "Utilisateur",
    ];

    const ACTION_INFOS = [
        CortestLogEntry::ACTION_CREER => "Création d'un objet",
        CortestLogEntry::ACTION_CALCULER => "Calcul de scores bruts ou étalonnés",
        CortestLogEntry::ACTION_MODIFIER => "Mise à jour d'un objet",
        CortestLogEntry::ACTION_SUPPRIMER => "Suppression d'un objet",
        CortestLogEntry::ACTION_EXPORTER => "Export de données",
        CortestLogEntry::ACTION_UTILISATEUR => "Action d'un utilisateur de CORTEST",
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface        $router,
        private readonly LoggerInterface        $logger,
    )
    {
    }

    /**
     * @param CortestLogEntry[] $logEntries
     * @return CortestLogEntryWrapper[]
     */
    public function processAll(array $logEntries): array
    {

        $result = [];

        foreach ($logEntries as $logEntry) {
            $result[] = $this->process($logEntry);
        }

        return $result;

    }

    private function process(CortestLogEntry $logEntry): CortestLogEntryWrapper
    {
        if ($logEntry->object_id != null && $logEntry->object_class != null) {
            $object = $this->entityManager->find(
                className: $logEntry->object_class,
                id: $logEntry->object_id
            );
        } else {
            $object = null;
        }

        return new CortestLogEntryWrapper(
            log: $logEntry,
            lien: $object ? $this->lien($object) : null,
            object: $object,
        );
    }

    public function lien(object $object): string|null
    {
        if ($object instanceof Concours) {
            return $this->router->generate("concours_index");
        } elseif ($object instanceof Correcteur) {
            return $this->router->generate("correcteur_consulter", ["id" => $object->id]);
        } elseif ($object instanceof CortestUser) {
            return $this->router->generate("admin_utilisateurs");
        } elseif ($object instanceof Etalonnage) {
            return $this->router->generate("etalonnage_index", ["id" => $object->id]);
        } elseif ($object instanceof Graphique) {
            return $this->router->generate("graphique_index", ["id" => $object->id]);
        } elseif ($object instanceof NiveauScolaire) {
            return $this->router->generate("niveau_scolaire_index");
        } elseif ($object instanceof QuestionTest) {
            return $this->router->generate("test_consulter", ["id" => $object->test->id]);
        } elseif ($object instanceof ReponseCandidat) {
            return $this->router->generate("session_consulter", ["id" => $object->session->id]);
        } elseif ($object instanceof Resource) {
            return $this->router->generate("home");
        } elseif ($object instanceof Session) {
            return $this->router->generate("session_consulter", ["id" => $object->id]);
        } elseif ($object instanceof Sgap) {
            return $this->router->generate("sgap_index");
        } elseif ($object instanceof Structure) {
            return $this->router->generate("structure_consulter", ["id" => $object->id]);
        } elseif ($object instanceof Test) {
            return $this->router->generate("test_consulter", ["id" => $object->id]);
        } else {
            $this->logger->error("Impossible de générer un lien : cas non implémenté", ["class" => $object::class]);
            return null;
        }
    }
}