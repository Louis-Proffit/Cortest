<?php

namespace App\Core\ReponseCandidat;

use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Repository\TestRepository;

readonly class ExportReponsesCandidat
{

    public function __construct(
        private ExportReponseCandidat $exportReponseCandidat
    )
    {
    }

    /**
     * @param ReponseCandidat[] $reponses
     * @return string[][]
     */
    public function export(array $reponses): array
    {

        $data = [];

        if (!empty($reponses)) {
            /** @var QuestionTest[] $questions */
            $questions = $reponses[0]->session->test->questions->toArray();

            foreach ($reponses as $reponseCandidat) {
                $data[] = $this->exportReponseCandidat->exportCandidatAndReponses($reponseCandidat, $questions);
            }
        }

        return $data;
    }

    /**
     * @param ReponseCandidat[] $reponses
     * @return string[][]
     */
    public function exportOrderedByIntitule(array $reponses,
                                            TestRepository $testRepository
    ): array
    {

        $data = [];

        if (!empty($reponses)) {
            /** @var QuestionTest[] $questions */
            $questions = $reponses[0]->session->test->questions->toArray();
            usort($questions, function($a, $b) {
                return $a->intitule <=> $b->intitule;
            });

            foreach ($reponses as $reponseCandidat) {
                $data[] = $this->exportReponseCandidat->exportCandidatAndReponses($reponseCandidat, $questions);
            }
        }

        return $data;
    }
}