<?php

namespace App\Core\Activite;

use App\Core\ScoreBrut\ScoresBruts;
use App\Core\ScoreEtalonne\ScoresEtalonnes;
use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\CortestLogEntry;
use App\Entity\CortestUser;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\NiveauScolaire;
use App\Entity\ReponseCandidat;
use App\Entity\Resource;
use App\Entity\Session;
use App\Entity\Sgap;
use App\Entity\Structure;
use App\Entity\Test;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ActiviteLogger
{

    const DATE_FORMAT = "d/m/y H:iw";

    public function __construct(
        private Security               $security,
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger
    )
    {
    }

    public function persistAction(
        string     $action,
        object     $object,
        string     $message,
        array|null $data = null
    ): void
    {
        $this->entityManager->persist(new CortestLogEntry(
            id: 0,
            action: $action,
            logged_at: $this->getLoggedDate(),
            object_id: $object->id,
            object_class: $object::class,
            data: ($data ?? []) + $this->getData($object),
            username: $this->getLoggedUsername(),
            message: $message
        ));
    }

    public function persist(
        string     $action,
        string     $message,
        array|null $data
    ): void
    {
        $this->entityManager->persist(new CortestLogEntry(
            id: 0,
            action: $action,
            logged_at: $this->getLoggedDate(),
            object_id: null,
            object_class: null,
            data: $data,
            username: $this->getLoggedUsername(),
            message: $message
        ));
    }

    public function persistExportCalcul(
        object     $calcul,
        string     $message,
        array|null $data = null,
    ): void
    {
        $this->entityManager->persist(new CortestLogEntry(
            id: 0,
            action: CortestLogEntry::ACTION_EXPORTER,
            logged_at: $this->getLoggedDate(),
            object_id: null,
            object_class: $calcul::class,
            data: ($data ?? []) + $this->getCalculData($calcul),
            username: $this->getLoggedUsername(),
            message: $message
        ));
    }

    public function persistCalcul(
        object     $calcul,
        string     $message,
        array|null $data = null,
    ): void
    {
        $this->entityManager->persist(new CortestLogEntry(
            id: 0,
            action: CortestLogEntry::ACTION_CALCULER,
            logged_at: $this->getLoggedDate(),
            object_id: null,
            object_class: $calcul::class,
            data: ($data ?? []) + $this->getCalculData($calcul),
            username: $this->getLoggedUsername(),
            message: $message
        ));
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    private function getLoggedDate(): DateTime
    {
        return new DateTime("now");
    }

    private function getLoggedUsername(): string
    {
        return $this->security->getUser()->getUserIdentifier();
    }

    public function getCalculData(object $calcul)
    {
        if ($calcul instanceof ScoresEtalonnes) {
            return ["etalonnage" => $calcul->etalonnage->nom] + $this->getCalculData($calcul->scoresBruts);
        } elseif ($calcul instanceof ScoresBruts) {
            return ["correcteur" => $calcul->correcteur->nom];
        } else {
            return [];
        }
    }

    public function getData(object $object): array
    {
        if ($object instanceof CortestUser) {
            return ["utilisateur" => $object->username, "role" => $object->role];
        } elseif ($object instanceof Correcteur) {
            return ["nom" => $object->nom, "structure" => $object->structure->nom];
        } elseif ($object instanceof Concours) {
            return ["intitule" => $object->intitule, "type" => $object->type_concours];
        } elseif ($object instanceof Etalonnage) {
            return ["nom" => $object->nom];
        } elseif ($object instanceof Graphique) {
            return ["nom" => $object->nom];
        } elseif ($object instanceof NiveauScolaire) {
            return ["nom" => $object->nom];
        } elseif ($object instanceof ReponseCandidat) {
            return ["nom" => $object->nom, "prenom" => $object->prenom, "date de naissance" => $object->date_de_naissance->format(self::DATE_FORMAT)];
        } elseif ($object instanceof Resource) {
            return ["nom" => $object->nom, "de l'utilisateur" => $object->user->username];
        } elseif ($object instanceof Session) {
            return ["date" => $object->date->format(self::DATE_FORMAT), "sgap" => $object->sgap->nom, "candidats" => $object->reponses_candidats->count()];
        } elseif ($object instanceof Sgap) {
            return ["nom" => $object->nom];
        } elseif ($object instanceof Structure) {
            return ["nom" => $object->nom];
        } elseif ($object instanceof Test) {
            return ["nom" => $object->nom, "version batterie" => $object->version_batterie];
        } else {
            $this->logger->error("Sauvegarde des données pour l'objet non implémenté : ", ["classe" => $object::class]);
            return [];
        }
    }
}