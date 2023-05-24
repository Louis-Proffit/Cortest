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
        ProfilRepository $profilRepository
    ): Response
    {
        $items = $profilRepository->findAll();

        return $this->render("profil/index.html.twig", ["items" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        Request                $request
    ): RedirectResponse|Response
    {
        $item = new Profil(
            id: 0,
            nom: "",
            echelles: new ArrayCollection(),
            etalonnages: new ArrayCollection(),
            graphiques: new ArrayCollection()
        );

        $form = $this->createForm(ProfilType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute("profil_index");

        }

        return $this->render("profil/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ProfilRepository       $profilRepository,
        EntityManagerInterface $entityManager,
        int                    $id): RedirectResponse
    {
        $item = $profilRepository->find($id);

        if ($item != null) {
            $entityManager->remove($item);
            $entityManager->flush();

            $this->addFlash("success", "Suppression du profil enregistrée.");
        } else {
            $this->addFlash("danger", "Le profil n'existe pas ou a déjà été supprimé.");
        }

        return $this->redirectToRoute("profil_index");
    }
}