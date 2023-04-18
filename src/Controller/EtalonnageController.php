<?php

namespace App\Controller;

use App\Core\Etalonnage\EtalonnageManager;
use App\Entity\EchelleEtalonnage;
use App\Entity\Etalonnage;
use App\Form\Data\EtalonnageCreer;
use App\Form\Data\EchelleEtalonnageGaussienCreer;
use App\Form\Data\EtalonnageGaussienCreer;
use App\Form\EtalonnageCreerType;
use App\Form\EtalonnageGaussienType;
use App\Form\EtalonnageType;
use App\Repository\EtalonnageRepository;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/etalonnage", name: "etalonnage_")]
class EtalonnageController extends AbstractController
{

    #[Route("/index", name: 'index')]
    public function index(
        EtalonnageRepository $etalonnage_repository
    ): Response
    {
        $etalonnages = $etalonnage_repository->findAll();

        return $this->render('etalonnage/index.html.twig',
            ["etalonnages" => $etalonnages]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        EtalonnageRepository $etalonnage_repository,
        int                  $id
    ): Response
    {
        $etalonnage = $etalonnage_repository->find($id);
        return $this->render("etalonnage/etalonnage.html.twig", ["etalonnage" => $etalonnage]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(): Response
    {
        return $this->render("etalonnage/index_creer.html.twig");
    }


    #[Route("/creer/simple", name: "creer_simple")]
    public function creerSimple(
        EntityManagerInterface $entity_manager,
        ProfilRepository       $profil_repository,
        Request                $request
    ): Response
    {
        $profils = $profil_repository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, créez en un");
            return $this->redirectToRoute("profil_index");
        }

        $etalonnageCreer = new EtalonnageCreer(
            profil: $profils[0],
            nombre_classes: 0,
            nom: ""
        );

        $form = $this->createForm(EtalonnageCreerType::class, $etalonnageCreer);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $nombreClasses = $etalonnageCreer->nombre_classes;
            $profil = $etalonnageCreer->profil;

            $etalonnage = new Etalonnage(
                id: 0,
                profil: $profil,
                nom: $etalonnageCreer->nom,
                nombre_classes: $nombreClasses,
                echelles: new ArrayCollection()
            );

            foreach ($profil->echelles as $echelle) {
                $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle(
                    $echelle,
                    $etalonnage,
                    $nombreClasses
                ));
            }

            $entity_manager->persist($etalonnage);
            $entity_manager->flush();


            return $this->redirectToRoute("etalonnage_modifier", ["id" => $etalonnage->id]);
        }

        return $this->render("etalonnage/creer_simple.html.twig", ["form" => $form]);
    }

    #[Route("/creer/gaussien", name: "creer_gaussien")]
    public function creerGaussien(
        EntityManagerInterface $entity_manager,
        ProfilRepository       $profil_repository,
        Request                $request
    ): Response
    {
        $profils = $profil_repository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, créez en un");
            return $this->redirectToRoute("profil_index");
        }

        $etalonnageCreer = new EtalonnageCreer(
            profil: $profils[0],
            nombre_classes: 0,
            nom: ""
        );

        $form = $this->createForm(EtalonnageCreerType::class, $etalonnageCreer);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $nombreClasses = $etalonnageCreer->nombre_classes;
            $profil = $etalonnageCreer->profil;

            $etalonnage = new Etalonnage(
                id: 0,
                profil: $profil,
                nom: $etalonnageCreer->nom,
                nombre_classes: $nombreClasses,
                echelles: new ArrayCollection()
            );

            foreach ($profil->echelles as $echelle) {
                $etalonnage->echelles->add(EchelleEtalonnage::rangeEchelle(
                    $echelle,
                    $etalonnage,
                    $nombreClasses
                ));
            }

            $entity_manager->persist($etalonnage);
            $entity_manager->flush();


            return $this->redirectToRoute("etalonnage_ajout_echelles_gaussiennes", ["id" => $etalonnage->id]);
        }

        return $this->render("etalonnage/creer_gaussien.html.twig", ["form" => $form]);
    }

    #[Route("/ajout/echelles/gaussiennes/{id}", name: "ajout_echelles_gaussiennes")]
    public function ajoutEchellesGaussiennes(
        EtalonnageRepository $etalonnage_repository,
        EntityManagerInterface $entity_manager,
        EtalonnageManager $etalonnageManager,
        Request              $request,
        int                  $id,
    ): Response
    {
        $etalonnage = $etalonnage_repository->find($id);


        $etalonnageGaussienCreer = new EtalonnageGaussienCreer(array());
        foreach ($etalonnage->echelles as $echelle){
            $etalonnageGaussienCreer->echelleEtalonnageGaussienCreer[$echelle->echelle->nom_php]=new EchelleEtalonnageGaussienCreer($echelle, 0, 1);
        }

        $form = $this->createForm(EtalonnageGaussienType::class, $etalonnageGaussienCreer, ['bounds_number' => $etalonnage->nombre_classes]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            foreach ($etalonnage->echelles as $echelle){
                $echelle->bounds = $etalonnageManager->calculateBounds(
                    $etalonnageGaussienCreer->echelleEtalonnageGaussienCreer[$echelle->echelle->nom_php]->mean,
                    $etalonnageGaussienCreer->echelleEtalonnageGaussienCreer[$echelle->echelle->nom_php]->stdDev,
                    $etalonnage->nombre_classes - 1,
                );
            }
            $entity_manager->persist($etalonnage);
            $entity_manager->flush();

            return $this->redirectToRoute("etalonnage_modifier", ["id" => $etalonnage->id]);
        }

        return $this->render("etalonnage/creer_gaussien.html.twig", ["form" => $form]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EtalonnageRepository $etalonnage_repository,
        ManagerRegistry      $doctrine,
        Request              $request,
        int                  $id,
    ): Response
    {
        $etalonnage = $etalonnage_repository->find($id);

        $form = $this->createForm(EtalonnageType::class, $etalonnage);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("etalonnage_consulter", ["id" => $etalonnage->id]);

        }

        return $this->render("etalonnage/modifier.html.twig", ["form" => $form]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entity_manager,
        EtalonnageRepository   $etalonnage_repository,
        int                    $id,
    ): Response
    {
        $etalonnage = $etalonnage_repository->find($id);

        if ($etalonnage != null) {
            $entity_manager->remove($etalonnage);

            foreach ($etalonnage->echelles as $echelle) {
                $entity_manager->remove($echelle);
            }
            $entity_manager->flush();
        }

        return $this->redirectToRoute("etalonnage_index");
    }

}