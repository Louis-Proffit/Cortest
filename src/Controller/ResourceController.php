<?php

namespace App\Controller;

use App\Core\ResourceManager;
use App\Entity\Resource;
use App\Form\ResourceType;
use App\Security\ResourceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/resource", name: "resource_")]
class ResourceController extends CortestAbstractController
{

    #[Route("/download/{id}", name: "download")]
    public function download(
        ResourceManager        $resourceManager,
        EntityManagerInterface $entityManager,
        Resource               $resource): Response
    {
        $filePath = $resourceManager->resourcefilePathOrNull($resource);

        if ($filePath == null) {
            $this->addFlash("danger", "Le fichier n'existe pas. Suppression de la resource");

            $entityManager->remove($resource);
            $entityManager->flush();

            return $this->redirectToRoute("home");
        } else {
            return $this->file(file: $filePath, fileName: $resource->file_nom);
        }
    }

    #[Route("/creer", name: "creer")]
    public function creer(Request                $request,
                          ResourceManager        $resourceManager,
                          EntityManagerInterface $entityManager): Response
    {
        $resource = new Resource(id: 0, nom: "", file_nom: "", user: $this->getNonNullUser());
        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($resource);
            $entityManager->flush();

            $result = $resourceManager->upload($form->get("file")->getData(), $resource);

            if (!$result) {
                $entityManager->remove($resource);
                $entityManager->flush();
                $this->addFlash("danger", "Echec de la mise en ligne du fichier");
            } else {
                $this->addFlash("success", "Resource enregistrée");
                return $this->redirectToRoute("home");
            }
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
        $this->denyAccessUnlessGranted(attribute: ResourceVoter::DELETE, subject: $resource);

        $resourceManager->delete($resource);

        $entityManager->remove($resource);
        $entityManager->flush();

        $this->addFlash("success", "Resource supprimée");

        return $this->redirectToRoute("home");
    }
}