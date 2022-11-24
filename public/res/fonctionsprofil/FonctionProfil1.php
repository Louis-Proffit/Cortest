<?php

use App\Core\Entities\FonctionProfil;

class FonctionProfil1 extends FonctionProfil
{

    function compute($grille): Profil1
    {
        if ($grille instanceof Grille1) {
            return new Profil1(
                $this->echelleSimple($grille),
                $this->echelleSimple($grille),
                $this->echelleSimple($grille),
                $this->echelleSimple($grille),
                $this->echelleSimple($grille),
                $this->echelleComposite($grille),
                $this->echelleComposite($grille),
                $this->echelleComposite($grille),
                $this->subtest($grille),
                $this->subtest($grille)
            );
        } else {
            throw new InvalidArgumentException("Batterie1 ne peut traiter que des grilles de type grille1");
        }
    }

    private function echelleSimple(Grille1 $grille): float
    {
        return $grille->ifEquals(0, 'A', 1) +
            $grille->ifEquals(1, 'B', 1);
    }

    private function echelleComposite(Grille1 $grille): float
    {
        return $grille->reponses[1] === 'a';
    }

    private function subtest(Grille1 $grille): float
    {
        return $grille->reponses[1] === 'a';
    }
}