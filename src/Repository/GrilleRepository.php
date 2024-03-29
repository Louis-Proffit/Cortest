<?php

namespace App\Repository;

use App\Core\Grille\Grille;
use App\Core\Grille\GrilleOctobre2019;

class GrilleRepository
{
    const GRILLE_OCTOBRE_2019_INDEX = 1;
    const INDEX = [self::GRILLE_OCTOBRE_2019_INDEX];
    /** @var Grille[] */
    private array $all;

    public function __construct(
        private readonly GrilleOctobre2019 $grille_octobre_2019,
    )
    {
        $this->all = [
            self::GRILLE_OCTOBRE_2019_INDEX => $this->grille_octobre_2019
        ];
    }

    /**
     * @return Grille[]
     */
    public function all(): array
    {
        return $this->all;
    }

    public function getFromIndex(int $index): Grille
    {
        return $this->all[$index];
    }

    /**
     * @return Grille[]
     */
    public function indexToInstance(): array
    {
        return $this->all;
    }

    public function indexChoices(): array
    {
        $result = [];

        foreach ($this->all as $index => $grille) {
            $result[$grille->nom] = $index;
        }

        return $result;
    }

    /**
     * @return Grille[]
     */
    public function choices(): array
    {
        $result = [];

        foreach ($this->all as $grille) {
            $result[$grille->nom] = $grille;
        }

        return $result;
    }

}