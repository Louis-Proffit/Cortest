<?php

namespace App\Controller;

use App\Entity\Sgap;
use App\Form\SgapType;
use App\Repository\SgapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/sgap", name: "sgap_")]
class SgapController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        SgapRepository $sgapRepository,
    ): Response
    {
        $sgaps = $sgapRepository->findAll();

        return $this->render("sgap/index.html.twig", ["sgaps" => $sgaps]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Sgap(
            id: 0,
            indice: 0,
            nom: ""
        );

        $form = $this->createForm(SgapType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute("sgap_index");

        }

        return $this->render("sgap/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        SgapRepository         $sgapRepository,
        EntityManagerInterface $entityManager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $sgapRepository->find($id);

        $form = $this->createForm(SgapType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("sgap_index");
        }

        return $this->render("sgap/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        SgapRepository         $sgapRepository,
        EntityManagerInterface $entityManager,
        int                    $id): RedirectResponse
    {
        $item = $sgapRepository->find($id);

        $entityManager->remove($item);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du SGAP enregistrée");

        return $this->redirectToRoute("sgap_index");
    }
}