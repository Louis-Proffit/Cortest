<?php

namespace App\Controller;

use App\Entity\NiveauScolaire;
use App\Form\NiveauScolaireType;
use App\Repository\NiveauScolaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(self::BASE_PATH, name: self::BASE_ROUTE)]
class NiveauScolaireController extends AbstractCrudController
{

    const BASE_ROUTE = "niveau_scolaire_";
    const BASE_PATH = "/niveau-scolaire";

    public function __construct(NiveauScolaireRepository $niveau_scolaire_repository)
    {
        parent::__construct($niveau_scolaire_repository, NiveauScolaireType::class, self::BASE_ROUTE . "index");
    }


    protected function produce(): NiveauScolaire
    {
        return new NiveauScolaire(
            id: 0, nom: ""
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_niveau_scolaire.html.twig", ["items" => $items]);
    }
}