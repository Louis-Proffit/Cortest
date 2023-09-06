<?php

namespace App\Core\ReponseCandidat;

use App\Core\Exception\DifferentSessionException;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Repository\ReponseCandidatRepository;


/**
 * Méthodes permettant de sauvegarder et récupérer la liste des réponses actuellement traitée.
 * Cette liste est la référence pour le calcul des scores et des profils, l'export de fichiers csv et pdf
 * Elle peut être alimentée dans l'onglet recherche ou en consultant une session
 * Elle est consommée par les rubriques indiquées ci-dessus
 * Elle doit en permanence faire référence à une unique session.
 * Si ce n'est pas le cas, une exception {@link DifferentSessionException} doit être lancée, à la responsabilité de l'appelant
 * Seuls les identifiants de la réponse sont stockés, et l'ORM est appelé dès qu'une réponse est demandée.
 */
readonly class ReponsesCandidatStorage
{

    public function __construct(
        private ReponseCandidatRepository      $reponseCandidatRepository,
        private ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage)
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
    private function set(array $reponsesCandidats): void
    {
        $reponsesCandidatsIds = array_map(fn(ReponseCandidat $reponseCandidat) => $reponseCandidat->id, $reponsesCandidats);
        $this->reponsesCandidatSessionStorage->set($reponsesCandidatsIds);
    }

    public function setFromSession(Session $session): void
    {
        $this->set($session->reponses_candidats->toArray());
    }
}