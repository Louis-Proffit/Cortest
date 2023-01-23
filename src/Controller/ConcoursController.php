<?php

namespace App\Controller;

use App\Entity\Concours;
use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\ConcoursRepository;
use App\Repository\EchelleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(self::BASE_PATH, name: self::BASE_ROUTE)]
class ConcoursController extends AbstractCrudController
{

    const BASE_ROUTE = "concours_";
    const BASE_PATH = "/concours";

    public function __construct(ConcoursRepository $concours_repository)
    {
        parent::__construct($concours_repository, ConcoursType::class, self::BASE_ROUTE . "index");
    }


    protected function produce(): Concours
    {
        return new Concours(
            id: 0,
            nom: "",
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_concours.html.twig", ["items" => $items]);
    }
}