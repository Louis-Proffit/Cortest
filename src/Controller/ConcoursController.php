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

    #[Route("/index", name: "index")]
    public function index(
        ConcoursRepository $concours_repository,
        GrilleRepository   $grille_repository,
    ): Response
    {
        $items = $concours_repository->findAll();

        $grilles = $grille_repository->indexToInstance();

        return $this->render("concours/index.html.twig", ["items" => $items, "grilles" => $grilles]);
    }

    #[Route("/consulter/{id}", name: "consulter")]
    public function consulter(
        ConcoursRepository $concours_repository,
        GrilleRepository   $grille_repository,
        int                $id
    ): Response
    {
        $concours = $concours_repository->find($id);

        $grille = $grille_repository->getFromIndex($concours->index_grille);

        return $this->render("concours/concours.html.twig", ["concours" => $concours, "grille" => $grille]);
    }

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

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ConcoursRepository     $concours_repository,
        EntityManagerInterface $entity_manager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $concours_repository->find($id);

        $form = $this->createForm(ConcoursType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("concours_consulter", ["id" => $id]);
        }

        return $this->render("concours/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ConcoursRepository     $concours_repository,
        EntityManagerInterface $entity_manager,
        int                    $id): RedirectResponse
    {
        $item = $concours_repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        $this->addFlash("success", "Suppression du concours enregistrée.");

        return $this->redirectToRoute("concours_index");
    }
}