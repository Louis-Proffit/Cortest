<?php

namespace App\Controller;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractCrudController extends AbstractController
{

    protected ServiceEntityRepository $repository;
    protected string $formClass;
    protected string $indexRoute;


    public function __construct(ServiceEntityRepository $repository, string $formClass, string $indexRoute)
    {
        $this->repository = $repository;
        $this->formClass = $formClass;
        $this->indexRoute = $indexRoute;
    }


    #[Route("/index", name: "index")]
    public function index(): Response
    {
        $items = $this->repository->findAll();

        return $this->renderIndex($items);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        Request                $request
    )
    {
        $item = $this->produce();

        $form = $this->createForm($this->formClass, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute($this->indexRoute);

        }

        return $this->renderCreer($form);

    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entity_manager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $this->repository->find($id);

        $form = $this->createForm($this->formClass, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute($this->indexRoute);
        }

        return $this->renderModifier($form);
    }

    #[Route("/supprimer", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entity_manager,
        int                    $id)
    {
        $item = $this->repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        return $this->redirectToRoute($this->indexRoute);
    }

    protected abstract function produce(): mixed;

    protected function renderCreer(FormInterface $form): Response
    {
        return $this->render("crud/form.html.twig", ["form" => $form]);
    }

    protected function renderModifier(FormInterface $form): Response
    {
        return $this->render("crud/form.html.twig", ["form" => $form]);
    }

    protected abstract function renderIndex(array $items): Response;
}