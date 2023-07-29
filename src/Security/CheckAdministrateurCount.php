<?php

namespace App\Security;

use App\Entity\CortestUser;
use App\Repository\CortestUserRepository;

/**
 * Opérations pour garantir la présence systématique d'au moins un administrateur
 */
readonly class CheckAdministrateurCount
{
    public function __construct(
        private CortestUserRepository $userRepository
    )
    {
    }

    /**
     * Teste si il y a au moins deux administrateur
     * @return bool
     */
    public function atLeastTwoAdministrateurs(): bool
    {
        return $this->userRepository->count(["role" => CortestUser::ROLE_ADMINISTRATEUR]) >= 2;
    }

    /**
     * Teste si il y a au moins un administrateur
     * @return bool
     */
    public function atLeastOneAdministrateur(): bool
    {
        return $this->userRepository->count(["role" => CortestUser::ROLE_ADMINISTRATEUR]) >= 1;
    }
}