<?php

namespace App\Controller;

use App\Entity\Concours;
use App\Entity\QuestionConcours;
use App\Form\ConcoursType;
use App\Form\CreerConcoursType;
use App\Repository\ConcoursRepository;
use App\Repository\GrilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/concours", name: "concours_")]
class ConcoursController extends AbstractController
{

    /**
     * Consulter la liste des concours
     * @param ConcoursRepository $concoursRepository
     * @param GrilleRepository $grilleRepository
     * @return Response
     */
    #[Route("/index", name: "index")]
    public function index(
        ConcoursRepository $concoursRepository,
        GrilleRepository   $grilleRepository,
    ): Response
    {
        $items = $concoursRepository->findAll();

        $grilles = $grilleRepository->indexToInstance();

        return $this->render("concours/index.html.twig", ["items" => $items, "grilles" => $grilles]);
    }

    /**
     * Consulter un concours
     * @param GrilleRepository $grilleRepository
     * @param Concours $concours
     * @return Response
     */
    #[Route("/consulter/{id}", name: "consulter")]
    public function consulter(
        GrilleRepository $grilleRepository,
        Concours         $concours,
    ): Response
    {
        $grille = $grilleRepository->getFromIndex($concours->index_grille);

        return $this->render("concours/concours.html.twig", ["concours" => $concours, "grille" => $grille]);
    }

    /**
     * Formulaire de création d'un concours
     * @param EntityManagerInterface $entity_manager
     * @param GrilleRepository $grille_repository
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        GrilleRepository       $grille_repository,
        Request                $request
    ): RedirectResponse|Response
    {
        $concours = new Concours(
            id: 0,
            nom: "",
            correcteurs: new ArrayCollection(),
            sessions: new ArrayCollection(),
            index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            type_concours: 0, version_batterie: 0, questions: new ArrayCollection()
        );

        $form = $this->createForm(CreerConcoursType::class, $concours);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            QuestionConcours::initQuestions($grille_repository, $concours);

            $entity_manager->persist($concours);
            $entity_manager->flush();

            return $this->redirectToRoute("concours_index");
        }

        return $this->render("concours/form_creer.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire de modification d'un concours
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param Concours $concours
     * @return Response
     */
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Concours               $concours,
    ): Response
    {
        $form = $this->createForm(ConcoursType::class, $concours);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("concours_consulter", ["id" => $concours->id]);
        }

        return $this->render("concours/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Supprime un concours
     * @param EntityManagerInterface $entityManager
     * @param Concours $concours
     * @return RedirectResponse
     */
    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entityManager,
        Concours               $concours): RedirectResponse
    {
        $entityManager->remove($concours);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du concours enregistrée.");

        return $this->redirectToRoute("concours_index");
    }
}