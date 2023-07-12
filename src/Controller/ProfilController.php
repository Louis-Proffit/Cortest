<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Form\CreerProfilType;
use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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

        $form = $this->createForm(CreerProfilType::class, $item);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            for ($i = 0; $i < $form[CreerProfilType::ECHELLES_COUNT_KEY]->getData(); $i++) {
                $echelle = new Echelle(
                    id: 0,
                    nom: "Echelle " . $i . " (A MODIFIER)",
                    nom_php: "echelle_" . $i . "_a_modifier",
                    type: Echelle::TYPE_ECHELLE_SIMPLE,
                    echelles_correcteur: new ArrayCollection(),
                    echelles_etalonnage: new ArrayCollection(),
                    echelles_graphiques: new ArrayCollection(),
                    profil: $item
                );
                $item->echelles->add($echelle);

                $entityManager->persist($echelle);
            }

            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute("profil_index");

        }

        return $this->render("profil/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Profil                 $profil
    ): RedirectResponse|Response
    {

        $form = $this->createForm(ProfilType::class, $profil);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("profil_index");
        }

        return $this->render("profil/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Profil                 $profil): RedirectResponse
    {
        $logger->info("Suppression du profil : " . $profil->id);

        $entityManager->remove($profil);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du profil enregistrée.");

        return $this->redirectToRoute("profil_index");
    }
}