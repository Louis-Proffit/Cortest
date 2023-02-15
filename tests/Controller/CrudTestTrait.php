<?php

namespace App\Tests\Controller;

use App\Entity\CortestUser;
use Symfony\Component\HttpFoundation\Request;

trait CrudTestTrait
{

    use LoginTestTrait;

    public function traitTestIndex(string $path, string $role = CortestUser::ROLE_ADMINISTRATEUR): void
    {

    }
}