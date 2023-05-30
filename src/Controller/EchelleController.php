<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\EchelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/echelle", name: "echelle_")]
class EchelleController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        EchelleRepository $echelleRepository
    ): Response
    {
        $items = $echelleRepository->findAll();

        return $this->render("echelle/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Echelle(
            id: 0,
            nom: "",
            nom_php: "", type: Echelle::TYPE_ECHELLE_SIMPLE
        );;

        $form = $this->createForm(EchelleType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute("echelle_index");

        }

        return $this->render("echelle/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Echelle                $echelle
    ): Response
    {
        $form = $this->createForm(EchelleType::class, $echelle);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("echelle_index");
        }

        return $this->render("echelle/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Echelle                $echelle): RedirectResponse
    {
        $entityManager->remove($echelle);

        $this->addFlash("success", "Echelle supprimée");
        $logger->info("Echelle supprimée : " . $echelle->id);


        $entityManager->flush();

        return $this->redirectToRoute("echelle_index");
    }
}