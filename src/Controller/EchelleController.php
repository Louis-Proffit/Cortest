<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\EchelleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(self::BASE_PATH, name: self::BASE_ROUTE)]
class EchelleController extends AbstractCrudController
{

    const BASE_ROUTE = "echelle_";
    const BASE_PATH = "/echelle";

    public function __construct(EchelleRepository $echelle_repository)
    {
        parent::__construct($echelle_repository, EchelleType::class, self::BASE_ROUTE . "index");
    }


    protected function produce(): Echelle
    {
        return new Echelle(
            id: 0,
            nom: "",
            nom_php: ""
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_echelle.html.twig");
    }
}