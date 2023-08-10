<?php

namespace App\Core\Activite;

use App\Entity\Concours;
use App\Entity\Correcteur;
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
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\LogEntryInterface;
use Symfony\Component\Routing\RouterInterface;

class LogEntryProcessor
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
        Test::class
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
        Test::class => "Test"
    ];

    const CLASS_INFOS = [
        Concours::class => "Concours, avec intitulé et type",
        Correcteur::class => "",
        CortestUser::class => "",
        Echelle::class => "",
        EchelleCorrecteur::class => "",
        EchelleEtalonnage::class => "",
        Etalonnage::class => "",
        Graphique::class => "",
        NiveauScolaire::class => "",
        QuestionTest::class => "",
        ReponseCandidat::class => "",
        Resource::class => "",
        Session::class => "Session d'examen",
        Sgap::class => "",
        Structure::class => "",
        Test::class => ""
    ];

    const ACTIONS = [
        LogEntryInterface::ACTION_CREATE,
        LogEntryInterface::ACTION_UPDATE,
        LogEntryInterface::ACTION_REMOVE,
    ];

    const ACTION_NAMES = [
        LogEntryInterface::ACTION_CREATE => "Création",
        LogEntryInterface::ACTION_UPDATE => "Mise à jour",
        LogEntryInterface::ACTION_REMOVE => "Suppression"
    ];

    const ACTION_INFOS = [
        LogEntryInterface::ACTION_CREATE => "Création d'un objet",
        LogEntryInterface::ACTION_UPDATE => "Mise à jour, modification d'un objet",
        LogEntryInterface::ACTION_REMOVE => "Suppression d'un objet. Le lien ne devrait pas être disponible."
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface        $router
    )
    {
    }

    /**
     * @param LogEntry[] $logEntries
     * @return LogEntryWrapper[]
     */
    public function processAll(array $logEntries): array
    {

        $result = [];

        foreach ($logEntries as $logEntry) {
            $result[] = $this->process($logEntry);
        }

        return $result;

    }

    private function process(LogEntry $logEntry): LogEntryWrapper
    {
        $object = $this->entityManager->find(
            className: $logEntry->getObjectClass(),
            id: $logEntry->getObjectId()
        );

        return new LogEntryWrapper(
            entry: $logEntry,
            class: $logEntry->getObjectClass(),
            action: $logEntry->getAction(),
            lien: $object ? $this->lien($object) : null,
            object: $object,
            message: "TODO"
        );
    }

    public function lien(object $object): string|null
    {
        if ($object instanceof Concours) {
            return $this->router->generate("concours_index");
        } elseif ($object instanceof Correcteur) {
            return $this->router->generate("correcteur_consulter", ["id" => $object->id]);
        } elseif ($object instanceof CortestUser) {
            return $this->router->generate("admin_index");
        } elseif ($object instanceof Echelle) {
            return $this->router->generate("echelle_index");
        } elseif ($object instanceof EchelleCorrecteur) {
            return $this->router->generate("correcteur_consulter", ["id" => $object->correcteur->id]);
        } elseif ($object instanceof EchelleEtalonnage) {
            return $this->router->generate("etalonnage_consulter", ["id" => $object->etalonnage->id]);
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
            return null;
        }
    }
}