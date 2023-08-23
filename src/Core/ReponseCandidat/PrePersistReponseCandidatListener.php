<?php

namespace App\Core\ReponseCandidat;

use App\Entity\ReponseCandidat;
use App\Repository\GrilleRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

/**
 * Cette classe formate les réponses des candidats avant leur insertion dans la base de données.
 * L'array de réponses doit être un array simple JSON (et pas associatif)
 * Sa longueur doit être le nombre de question de la grille, associée au test, associée à la session, associée au candidat.
 * Si ce n'est pas le cas, la réponse 0 (vide) est ajoutée.
 * Des notifications pourraient être ajoutées dans le cas ou des réponses manqueraient.
 */
#[AsEntityListener(event: Events::prePersist, method: "format", entity: ReponseCandidat::class)]
#[AsEntityListener(event: Events::preUpdate, method: "format", entity: ReponseCandidat::class)]
readonly class PrePersistReponseCandidatListener
{

    public function __construct(
        private GrilleRepository $grilleRepository
    )
    {
    }

    public function format(ReponseCandidat $reponseCandidat): void
    {
        $grille = $this->grilleRepository->getFromIndex($reponseCandidat->session->test->index_grille);
        $nombreQuestions = $grille->nombre_questions;

        $trueReponses = [];

        for ($questionIndex = 1; $questionIndex <= $nombreQuestions; $questionIndex++) {
            if (key_exists($questionIndex, $reponseCandidat->reponses)) {
                $trueReponses[] = $reponseCandidat->reponses[$questionIndex];
            } else {
                $trueReponses[] = 0;
            }
        }

        $reponseCandidat->reponses = $trueReponses;
    }
}