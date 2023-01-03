<?php

namespace App\Controller;

use App\Core\Entities\ProfilOuScore;
use App\Core\Entities\ScoreComputer;
use App\Entity\CandidatReponse;
use App\Entity\DefinitionEtalonnageComputer;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use App\Repository\DefinitionEtalonnageComputerRepository;
use App\Repository\RuntimeResourcesRepository;
use Doctrine\Persistence\ManagerRegistry;
use HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionProfilController extends AbstractController
{

    /**
     * @throws HttpException
     */
    #[Route('/session_profils_form/{session_id}/{score_computer_id}', name: "session_profil_form")]
    public function sessionProfilForm(ManagerRegistry $doctrine, Request $request, int $session_id, int $score_computer_id): Response
    {
        $manager = $doctrine->getManager();

        /** @var Session $session */
        $session = $manager->find(Session::class, $session_id);

        /** @var DefinitionScoreComputer $score_computer */
        $score_computer = $manager->find(DefinitionScoreComputer::class, $score_computer_id);

        if ($session->grille->id != $score_computer->grille->id) {
            throw new HttpException("Le calculateur de score ne s'applique pas à la grille de la session considérée",
                Response::HTTP_BAD_REQUEST);
        }

        /** @var DefinitionEtalonnageComputerRepository $etalonnage_computer_repository */
        $etalonnage_computer_repository = $doctrine->getRepository(DefinitionEtalonnageComputer::class);
        $etalonnage_computers = $etalonnage_computer_repository->findByScoreDefinition($score_computer->score);

        $form = $this->createFormBuilder()
            ->add("id", ChoiceType::class, [
                    'label' => "Calculateur d'étalonnage à utiliser",
                    'choices' => array_combine(
                        array_map(fn(DefinitionEtalonnageComputer $etalonnageComputer) => $etalonnageComputer->nom,
                            $etalonnage_computers),
                        array_map(fn(DefinitionEtalonnageComputer $etalonnageComputer) => $etalonnageComputer->id,
                            $etalonnage_computers)
                    )
                ]
            )->add("Calculer", SubmitType::class)->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('consulter_session_profils',
                ["session_id" => $session_id, "score_computer_id" => $score_computer_id, "etalonnage_computer_id" => $form->getData()["id"]]);
        }

        return $this->render('scores_profils/session_profil_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/session_profils/{session_id}/{score_computer_id}/{etalonnage_computer_id}', name: "consulter_session_profils")]
    public function sessionProfils(
        ManagerRegistry            $doctrine,
        RuntimeResourcesRepository $runtime_resources_repository,
        int                        $session_id,
        int                        $score_computer_id,
        int                        $etalonnage_computer_id): Response
    {
        $manager = $doctrine->getManager();

        /** @var DefinitionScoreComputer $definition_score_computer */
        $definition_score_computer = $manager->find(DefinitionScoreComputer::class, $score_computer_id);
        /** @var DefinitionEtalonnageComputer $definition_etalonnage_computer */
        $definition_etalonnage_computer = $manager->find(DefinitionEtalonnageComputer::class, $etalonnage_computer_id);
        /** @var Session $session */
        $session = $manager->find(Session::class, $session_id);

        $score_computer = $runtime_resources_repository->scoreComputer($definition_score_computer);

        $scores = array_map(fn(CandidatReponse $reponse) => $score_computer->compute($reponse),
            $session->candidats->toArray());

        $etalonnage_computer = $runtime_resources_repository->etalonnageComputer($definition_etalonnage_computer);

        $profils = array_map(fn(ProfilOuScore $score) => $etalonnage_computer->compute($score),
            $scores);

        return $this->render("scores_profils/profil_cahier_des_charges.html.twig",
            ["scores_ou_profils" => $profils, "session_id" => $session_id, "score_computer_id" => $score_computer_id, "etalonnage_computer_id" => $etalonnage_computer_id]);
    }

}