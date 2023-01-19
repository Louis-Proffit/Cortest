<?php

namespace App\Controller;

use App\Entity\Sgap;
use App\Form\SgapType;
use App\Repository\SgapRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/sgap", name: "sgap_")]
class SgapController extends AbstractCrudController
{

    public function __construct(SgapRepository $sgap_repository)
    {
        parent::__construct($sgap_repository, SgapType::class, "sgap_index");
    }


    protected function produce(): Sgap
    {
        return new Sgap(
            id: 0,
            indice: 0,
            nom: ""
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_sgaps.html.twig", ["items" => $items]);
    }
}