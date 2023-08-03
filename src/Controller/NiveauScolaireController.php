<?php

namespace App\Controller;

use App\Entity\NiveauScolaire;
use App\Form\NiveauScolaireType;
use App\Repository\NiveauScolaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/niveau-scolaire", name: "niveau_scolaire_")]
class NiveauScolaireController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        NiveauScolaireRepository $niveauScolaireRepository
    ): Response
    {
        $items = $niveauScolaireRepository->findBy(criteria: [], orderBy: ["indice" => "ASC"]);

        return $this->render("niveau_scolaire/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new NiveauScolaire(id: 0, indice: 0, nom: "");

        $form = $this->createForm(NiveauScolaireType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute("niveau_scolaire_index");

        }

        return $this->render("niveau_scolaire/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        NiveauScolaireRepository $niveau_scolaire_repository,
        EntityManagerInterface   $entity_manager,
        Request                  $request,
        int                      $id
    ): Response
    {
        $item = $niveau_scolaire_repository->find($id);

        $form = $this->createForm(NiveauScolaireType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("niveau_scolaire_index");
        }

        return $this->render("niveau_scolaire/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/confirmer/{id}", name: "supprimer_confirmer")]
    public function supprimerConfirmer(NiveauScolaire $niveauScolaire): Response
    {
        return $this->render("niveau_scolaire/supprimer.html.twig", ["niveau_scolaire" => $niveauScolaire]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entityManager,
        NiveauScolaire         $niveauScolaire): RedirectResponse
    {
        $entityManager->remove($niveauScolaire);
        $entityManager->flush();

        $this->addFlash("success", "Suppression effectuée.");

        return $this->redirectToRoute("niveau_scolaire_index");
    }
}