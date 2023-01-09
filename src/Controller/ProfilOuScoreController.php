<?php

namespace App\Controller;

use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profil_ou_score", name: "profil_ou_score_")]
class ProfilOuScoreController extends AbstractController
{

    #[Route("/consulter", name: "consulter")]
    public function consulter(
        ProfilOuScoreRepository $profil_ou_score_repository
    ): Response
    {
        $profil_ou_scores = $profil_ou_score_repository->all();

        return $this->render("profil_ou_score/index.html.twig", ["profil_ou_score" => $profil_ou_scores]);
    }

}