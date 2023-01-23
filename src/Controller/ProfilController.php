<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profil", name: "profil_")]
class ProfilController extends AbstractCrudController
{

    public function __construct(
        ProfilRepository $profil_repository,
    )
    {
        parent::__construct($profil_repository, ProfilType::class, "profil_index");
    }


    protected function produce(): Profil
    {
        return new Profil(
            id: 0,
            nom: "",
            echelles: new ArrayCollection(),
            etalonnages: new ArrayCollection(),graphiques: new ArrayCollection()
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_profils.html.twig", ["items" => $items]);
    }
}