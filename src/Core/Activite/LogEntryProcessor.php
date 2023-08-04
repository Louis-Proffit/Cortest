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
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Loggable\LogEntryInterface;
use Symfony\Component\Routing\RouterInterface;

class LogEntryProcessor
{

    const CLASSES = [
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

    const ACTIONS = [
        LogEntryInterface::ACTION_CREATE => "Création",
        LogEntryInterface::ACTION_UPDATE => "Mise à jour",
        LogEntryInterface::ACTION_REMOVE => "Suppression"
    ];

    private LogEntryRepository $logEntryRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface        $router
    )
    {
        $this->logEntryRepository = $entityManager->getRepository(LogEntry::class);
    }


    /**
     * @return LogEntryWrapper[]
     */
    public function findAll(): array
    {
        $logEntries = $this->logEntryRepository->findBy([], orderBy: ["loggedAt" => "DESC"]);

        $result = [];

        foreach ($logEntries as $logEntry) {
            $object = $this->entityManager->find($logEntry->getObjectClass(), $logEntry->getObjectId());
            $result[] = new LogEntryWrapper(
                entry: $logEntry,
                class: self::CLASSES[$logEntry->getObjectClass()],
                action: self::ACTIONS[$logEntry->getAction()],
                lien: $object ? $this->lien($object) : null,
                object: $object,
                message: "TODO"
            );

        }

        return $result;

    }

    public function lien(object $object): string|null
    {
        return match ($object::class) {
            Concours::class => $this->router->generate("concours_index"),
            Correcteur::class => $this->router->generate("correcteur_consulter", ["id" => $object->id]),
            CortestUser::class => $this->router->generate("admin_index"),
            Echelle::class => $this->router->generate("echelle_index"),
            EchelleCorrecteur::class => $this->router->generate("correcteur_consulter", ["id" => $object->correcteur->id]),
            EchelleEtalonnage::class => $this->router->generate("etalonnage_consulter", ["id" => $object->etalonnage->id]),
            Etalonnage::class => $this->router->generate("etalonnage_index", ["id" => $object->id]),
            Graphique::class => $this->router->generate("graphique_index", ["id" => $object->id]),
            NiveauScolaire::class => $this->router->generate("niveau_scolaire_index"),
            QuestionTest::class => $this->router->generate("test_consulter", ["id" => $object->test->id]),
            ReponseCandidat::class => $this->router->generate("session_consulter", ["id" => $object->session->id]),
            Resource::class => $this->router->generate("home"),
            Session::class => $this->router->generate("session_consulter", ["id" => $object->id]),
            Sgap::class => $this->router->generate("sgap_index"),
            Structure::class => $this->router->generate("structure_consulter", ["id" => $object->id]),
            Test::class => $this->router->generate("test_consulter", ["id" => $object->id]),
            default => null
        };
    }

}