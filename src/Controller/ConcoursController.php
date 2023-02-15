<?php

namespace App\Controller;

use App\Entity\Concours;
use App\Form\ConcoursType;
use App\Repository\ConcoursRepository;
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
        ConcoursRepository $concours_repository
    ): Response
    {
        $items = $concours_repository->findAll();

        return $this->render("concours/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Concours(
            id: 0,
            nom: "",
        );

        $form = $this->createForm(ConcoursType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute("concours_index");

        }

        return $this->render("concours/form.html.twig", ["form" => $form->createView()]);
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

        $form = $this->createForm(ConcoursRepository::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("concours_index");
        }

        return $this->render("concours/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer", name: "supprimer")]
    public function supprimer(
        ConcoursRepository     $concours_repository,
        EntityManagerInterface $entity_manager,
        int                    $id)
    {
        $item = $concours_repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        return $this->redirectToRoute("concours_index");
    }
}