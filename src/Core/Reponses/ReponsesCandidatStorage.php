<?php

namespace App\Core\Reponses;

use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Recherche\ReponsesCandidatSessionStorage;
use App\Repository\ReponseCandidatRepository;

class ReponsesCandidatStorage
{

    public function __construct(
        private readonly ReponseCandidatRepository      $reponseCandidatRepository,
        private readonly ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage)
    {
    }

    /**
     * @return ReponseCandidat[]
     */
    public function get(): array
    {
        $reponsesCandidatsIds = $this->reponsesCandidatSessionStorage->getOrSetDefault(array());
        return $this->reponseCandidatRepository->findAllByIds($reponsesCandidatsIds);
    }

    /**
     * @param ReponseCandidat[] $reponsesCandidats
     * @return void
     */
    public function set(array $reponsesCandidats): void
    {
        $reponsesCandidatsIds = array_map(fn(ReponseCandidat $reponseCandidat) => $reponseCandidat->id, $reponsesCandidats);
        $this->reponsesCandidatSessionStorage->set($reponsesCandidatsIds);
    }

    public function setFromSession(Session $session): void
    {
        $this->set($session->reponses_candidats->toArray());
    }
}