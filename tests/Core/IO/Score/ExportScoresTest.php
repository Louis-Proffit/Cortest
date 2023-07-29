<?php

namespace App\Tests\Core\IO\Score;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\IO\Score\ExportScores;
use App\Repository\CorrecteurRepository;
use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExportScoresTest extends KernelTestCase
{

    public function testAllExported()
    {
        $session = self::getContainer()->get(SessionRepository::class)->findOneBy([]);
        $correcteur = self::getContainer()->get(CorrecteurRepository::class)->findOneBy([]);

        $correcteurManager = self::getContainer()->get(CorrecteurManager::class);

        $scores = $correcteurManager->corriger($correcteur, $session->reponses_candidats->toArray());

        /** @var ExportScores $exportScores */
        $exportScores = self::getContainer()->get(ExportScores::class);

        $raw = $exportScores->export($correcteur->structure, $scores, $session->reponses_candidats->toArray());
        self::assertCount($session->reponses_candidats->count(), $raw);
    }

}
