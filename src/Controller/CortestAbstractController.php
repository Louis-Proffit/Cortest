<?php

namespace App\Controller;

use App\Entity\CortestUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CortestAbstractController extends AbstractController
{

    protected function getUser(): ?CortestUser
    {
        /** @var CortestUser|null $user */
        $user = parent::getUser();
        return $user;
    }

    protected function getNonNullUser(): CortestUser
    {
        /** @var CortestUser $user */
        $user = $this->getUser();
        return $user;
    }

}