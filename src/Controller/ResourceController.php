<?php

namespace App\Controller;

use App\Core\ResourceManager;
use App\Entity\Resource;
use App\Form\ResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/resource", name: "resource_")]
class ResourceController extends AbstractController
{

    #[Route("/download/{id}", name: "download")]
    public function download(
        ResourceManager $resourceManager,
        Resource        $resource): BinaryFileResponse
    {
        $filePath = $resourceManager->resourceFilePath($resource);
        return $this->file(file: $filePath, fileName: $resource->file_nom);
    }

    #[Route("/creer", name: "creer")]
    public function creer(Request                $request,
                          ResourceManager        $resourceManager,
                          EntityManagerInterface $entityManager): Response
    {
        $resource = new Resource(id: 0, nom: "", file_nom: "", user: $this->getUser());
        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($resource);
            $entityManager->flush();

            $resourceManager->upload($form->get("file")->getData(), $resource);

            return $this->redirectToRoute("home");
        }

        return $this->render("resource/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entityManager,
        ResourceManager        $resourceManager,
        Resource               $resource
    ): RedirectResponse
    {
        $resourceManager->delete($resource);

        $entityManager->remove($resource);
        $entityManager->flush();

        $this->addFlash("info", "Resource supprimée");

        return $this->redirectToRoute("home");
    }
}