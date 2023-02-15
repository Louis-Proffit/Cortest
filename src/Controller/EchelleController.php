<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\EchelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/echelle", name: "echelle_")]
class EchelleController extends AbstractController
{


    #[Route("/index", name: "index")]
    public function index(
        EchelleRepository $echelle_repository
    ): Response
    {
        $items = $echelle_repository->findAll();

        return $this->render("echelle/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
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

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute("echelle_index");

        }

        return $this->render("echelle/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EchelleRepository      $echelle_repository,
        EntityManagerInterface $entity_manager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $echelle_repository->find($id);

        $form = $this->createForm(EchelleType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("echelle_index");
        }

        return $this->render("echelle/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EchelleRepository      $echelle_repository,
        EntityManagerInterface $entity_manager,
        int                    $id): RedirectResponse
    {
        $item = $echelle_repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        return $this->redirectToRoute("echelle_index");
    }
}