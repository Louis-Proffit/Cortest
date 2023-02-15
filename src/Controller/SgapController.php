<?php

namespace App\Controller;

use App\Entity\Sgap;
use App\Form\SgapType;
use App\Repository\SgapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/sgap", name: "sgap_")]
class SgapController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        SgapRepository $sgap_repository,
    ): Response
    {
        $items = $sgap_repository->findAll();

        return $this->render("sgap/index.html.twig", $items);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
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

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute("sgap_index");

        }

        return $this->render("sgap/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        SgapRepository         $sgap_repository,
        EntityManagerInterface $entity_manager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $sgap_repository->find($id);

        $form = $this->createForm(SgapType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("sgap_index");
        }

        return $this->render("sgap/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer", name: "supprimer")]
    public function supprimer(
        SgapRepository         $sgap_repository,
        EntityManagerInterface $entity_manager,
        int                    $id): RedirectResponse
    {
        $item = $sgap_repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        return $this->redirectToRoute("sgap_index");
    }
}