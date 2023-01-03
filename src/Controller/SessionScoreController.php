<?php

namespace App\Controller;

use App\Entity\CandidatReponse;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use App\Repository\DefinitionScoreComputerRepository;
use App\Repository\RuntimeResourcesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionScoreController extends AbstractController
{

    #[Route('/session_scores_form/{session_id}', name: "session_score_form")]
    public function sessionScoreForm(ManagerRegistry $doctrine, Request $request, int $session_id): Response
    {
        $manager = $doctrine->getManager();
        /** @var Session $session */
        $session = $manager->find(Session::class, $session_id);
        /** @var DefinitionScoreComputerRepository $definition_score_computer_repository */
        $definition_score_computer_repository = $doctrine->getRepository(DefinitionScoreComputer::class);
        $score_computers = $definition_score_computer_repository->findByGrilleDefinition($session->grille);

        $form = $this->createFormBuilder()
            ->add(
                "id", ChoiceType::class, [
                    'label' => "Calculateur de score à utiliser",
                    'choices' => array_combine(
                        array_map(fn(DefinitionScoreComputer $score_computer) => $score_computer->nom,
                            $score_computers),
                        array_map(fn(DefinitionScoreComputer $score_computer) => $score_computer->id,
                            $score_computers)
                    )
                ]
            )->add("Calculer", SubmitType::class)->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('consulter_session_scores',
                ["session_id" => $session_id, "score_computer_id" => $form->getData()["id"]]);
        }

        return $this->render('scores_profils/session_score_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/session_scores/{session_id}/{score_computer_id}', name: "consulter_session_scores")]
    public function sessionScores(ManagerRegistry $doctrine, RuntimeResourcesRepository $runtime_resources_repository, int $session_id, int $score_computer_id): Response
    {
        $manager = $doctrine->getManager();
        /** @var DefinitionScoreComputer $definition_score_computer */
        $definition_score_computer = $manager->find(DefinitionScoreComputer::class, $score_computer_id);

        /** @var Session $session */
        $session = $manager->find(Session::class, $session_id);

        $score_computer = $runtime_resources_repository->scoreComputer($definition_score_computer);

        $scores = array_map(fn(CandidatReponse $reponse) => $score_computer->compute($reponse),
            $session->candidats->toArray());

        return $this->render("scores_profils/score_cahier_des_charges.html.twig",
            ["scores_ou_profils" => $scores, "session_id" => $session_id, "score_computer_id" => $score_computer_id]);

    }


}