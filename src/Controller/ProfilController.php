<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profil", name: "profil_")]
class ProfilController extends AbstractController
{


    #[Route("/index", name: "index")]
    public function index(
        ProfilRepository $profil_repository
    ): Response
    {
        $items = $profil_repository->findAll();

        return $this->render("profil/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Profil(
            id: 0,
            nom: "",
            echelles: new ArrayCollection(),
            etalonnages: new ArrayCollection(), graphiques: new ArrayCollection()
        );

        $form = $this->createForm(ProfilType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($item);
            $entity_manager->flush();

            return $this->redirectToRoute("profil_index");

        }

        return $this->render("profil/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ProfilRepository       $profil_repository,
        EntityManagerInterface $entity_manager,
        int                    $id): RedirectResponse
    {
        $item = $profil_repository->find($id);

        $entity_manager->remove($item);
        $entity_manager->flush();

        $this->addFlash("success", "Suppression du profil enregistrée.");

        return $this->redirectToRoute("profil_index");
    }
}