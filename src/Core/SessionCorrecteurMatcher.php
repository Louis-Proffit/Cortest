<?php

namespace App\Core;

use App\Entity\Correcteur;
use App\Entity\Session;

/**
 * @see self::match()
 */
class SessionCorrecteurMatcher
{

    /**
     * Vérifie que la session et le correcteur sont compatibles.
     * Pour cela, vérifie que les deux réfèrent au même concours
     * @param Session $session
     * @param Correcteur $correcteur
     * @return bool
     */
    public function match(Session $session, Correcteur $correcteur): bool
    {
        $testId = $session->test->id;

        foreach ($correcteur->tests as $test) {
            if ($test->id == $testId) {
                return true;
            }
        }
        return false;
    }
}