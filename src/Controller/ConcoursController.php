<?php

namespace App\Controller;

use App\Entity\Concours;
use App\Form\ConcoursType;
use App\Repository\ConcoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
     * @return Response
     */
    #[Route("/index", name: "index")]
    public function index(
        ConcoursRepository $concoursRepository
    ): Response
    {
        $concours = $concoursRepository->findAll();

        return $this->render("concours/index.html.twig", ["concours" => $concours]);
    }


    /**
     * Formulaire de création d'un concours
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $concours = new Concours(
            id: 0,
            nom: "",
            type_concours: 0,
            tests: new ArrayCollection()
        );

        $form = $this->createForm(ConcoursType::class, $concours);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($concours);
            $entityManager->flush();

            $this->addFlash("success", "Le concours a été créé.");

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

            $this->addFlash("success", "Le concours a été modifié.");

            return $this->redirectToRoute("concours_index", ["id" => $concours->id]);
        }

        return $this->render("concours/form_modifier.html.twig", ["form" => $form->createView()]);
    }


    #[Route("/supprimer/confirmer/{id}", name: "supprimer_confirmer")]
    public function supprimerConfirmer(Concours $concours): Response
    {
        return $this->render("concours/supprimer.html.twig", [
            "concours" => $concours,
            "supprimable" => Concours::supprimable($concours)
        ]);
    }

    /**
     * Supprime un concours
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param Concours $concours
     * @return RedirectResponse
     */
    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Concours               $concours): RedirectResponse
    {
        if (!Concours::supprimable($concours)) {
            $logger->error("Impossible de supprimer le concours", ["concours" => $concours]);
            $this->addFlash("danger", "Impossible de supprimer le concours");

            return $this->redirectToRoute("concours_supprimer_confirmer", ["id" => $concours->id]);
        } else {
            $logger->info("Suppression du concours", ["concours" => $concours]);
            $this->addFlash("success", "Le concours a été supprimé.");

            $entityManager->remove($concours);
            $entityManager->flush();

            return $this->redirectToRoute("concours_index");
        }
    }
}