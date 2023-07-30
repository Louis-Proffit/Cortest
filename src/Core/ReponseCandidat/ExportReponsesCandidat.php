<?php

namespace App\Core\ReponseCandidat;

use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;

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
            $questions = $reponses[0]->session->test->questions;

            foreach ($reponses as $reponseCandidat) {
                $data[] = $this->exportReponseCandidat->export(
                    reponsesCandidat: $reponseCandidat,
                    questions: $questions
                );
            }
        }

        return $data;
    }
}