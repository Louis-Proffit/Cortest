<?php

namespace App\Controller;

use App\Core\Computer\GrilleComputer;
use App\Core\Computer\ProfilComputer;
use App\Core\Computer\ScoreComputer;
use App\Core\Entities\ProfilOuScore;
use App\Entity\CandidatReponse;
use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use App\Form\Data\ParametresCalculProfil;
use App\Form\ParametresCalculProfilType;
use App\Repository\DefinitionProfilComputerRepository;
use App\Repository\DefinitionScoreComputerRepository;
use App\Repository\RuntimeResourcesRepository;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session-profil", name: "profil_")]
class SessionProfilController extends AbstractController
{

    #[Route('/{session_id}/{score_computer_id}', name: "form")]
    public function sessionProfilForm(
        Request                            $request,
        SessionRepository                  $session_repository,
        DefinitionScoreComputerRepository  $score_computer_repository,
        DefinitionProfilComputerRepository $definition_etalonnage_computer_repository,
        ProfilComputer                     $profil_computer,
        ScoreComputer                      $score_computer,
        GrilleComputer                     $grille_computer,
        int                                $session_id,
        int                                $score_computer_id): Response
    {
        $session = $session_repository->find($session_id);

        $definition_score_computer = $score_computer_repository->find($score_computer_id);

        if ($session->grille->id != $definition_score_computer->grille->id) {
            throw new HttpException(Response::HTTP_BAD_REQUEST,
                "Le calculateur de score ne s'applique pas à la grille de la session considérée",);
        }

        $parametres_calcul_profil = new ParametresCalculProfil();
        $form = $this->createForm(
            ParametresCalculProfilType::class,
            $parametres_calcul_profil,
            [ParametresCalculProfilType::DEFINITION_SCORE_OPTION => $definition_score_computer->score]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $definition_etalonnage_computer = $parametres_calcul_profil->definition_etalonnage_computer;

            $reponses = $session->candidats->toArray();

            $grilles = $grille_computer->computeAll($reponses, $session->grille);

            $scores = $score_computer->computeAll($grilles, $definition_score_computer);

            $profils = $profil_computer->computeAll($scores, $definition_etalonnage_computer);


            return $this->render("profils/cahier_des_charges.html.twig",
                ["profils" => $profils,
                    "reponses" => $grilles,
                    "session" => $session,
                    "etalonnage_computer" => $definition_etalonnage_computer]);
        }

        return $this->render('profils/profil_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}