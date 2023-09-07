<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Core\Exception\UploadFailException;
use App\Core\IO\ResourceFileRepository;
use App\Entity\CortestLogEntry;
use App\Entity\Resource;
use App\Form\ResourceType;
use App\Security\DeleteResourceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/resource", name: "resource_")]
class ResourceController extends CortestAbstractController
{

    #[Route("/telecharger/{id}", name: "telecharger")]
    public function download(
        ResourceFileRepository $resourceFileManager,
        EntityManagerInterface $entityManager,
        Resource               $resource): Response
    {
        $filePath = $resourceFileManager->entityFilePathOrNull($resource);

        if ($filePath == null) {
            $this->addFlash("danger", "Le fichier n'existe pas. Suppression de la resource");

            $entityManager->remove($resource);
            $entityManager->flush();

            return $this->redirectToRoute("home");
        } else {
            return $this->file(file: $filePath, fileName: $resource->file_nom);
        }
    }

    /**
     * @throws UploadFailException
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        ActiviteLogger         $activiteLogger,
        Request                $request,
        ResourceFileRepository $resourceFileManager,
        EntityManagerInterface $entityManager): Response
    {
        $resource = new Resource(id: 0, nom: "", file_nom: "");
        $form = $this->createForm(ResourceType::class, $resource);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($resource);
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $resource,
                message: "Création d'une ressource"
            );
            $entityManager->flush();

            $file = $form->get("file")->getData();
            $resourceFileManager->upload($file, $resource);

            return $this->redirectToRoute("home");
        }

        return $this->render("resource/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        ResourceFileRepository $resourceFileManager,
        Resource               $resource
    ): RedirectResponse
    {
        $this->denyAccessUnlessGranted(attribute: DeleteResourceVoter::DELETE, subject: $resource);

        $resourceFileManager->delete($resource);

        $activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_SUPPRIMER,
            object: $resource,
            message: "Suppression d'une resource"
        );
        $entityManager->remove($resource);
        $entityManager->flush();

        $this->addFlash("success", "Resource supprimée");

        return $this->redirectToRoute("home");
    }
}