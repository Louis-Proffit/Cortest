<?php

namespace App\Controller;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Grille\GrilleRepository;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Form\CorrecteurCreerType;
use App\Form\CorrecteurType;
use App\Form\Data\CorrecteurCreer;
use App\Repository\CorrecteurRepository;
use App\Repository\EchelleRepository;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\isNull;

#[Route("/correcteur", name: "correcteur_")]
class CorrecteurController extends AbstractController
{
    #[Route("/index", name: 'index')]
    public function index(
        CorrecteurRepository $correcteur_repository
    ): Response
    {
        $correcteurs = $correcteur_repository->findAll();

        $grilles = [];

        foreach ($correcteurs as $correcteur) {
            $grilles[$correcteur->id] = new ($correcteur->grille_class)();
        }

        return $this->render('correcteur/index.html.twig',
            ["correcteurs" => $correcteurs, "grilles" => $grilles]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        CorrecteurRepository $correcteur_repository,
        int                  $id
    ): Response
    {
        $correcteur = $correcteur_repository->find($id);
        $grille = new ($correcteur->grille_class)();
        return $this->render("correcteur/correcteur.html.twig",
            ["correcteur" => $correcteur, "grille" => $grille]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        ProfilRepository       $profil_repository,
        GrilleRepository       $grille_repository,
        Request                $request
    ): Response
    {
        $profils = $profil_repository->findAll();
        $grilles = $grille_repository->classNames();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créer un");
            return $this->redirectToRoute("profil_index");
        }

        $correcteurCreer = new CorrecteurCreer(
            profil: $profils[0],
            grille_class: $grilles[0],
            nom: ""
        );
        $form = $this->createForm(CorrecteurCreerType::class, $correcteurCreer);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil = $correcteurCreer->profil;

            $correcteur = new Correcteur(
                id: 0,
                grille_class: $correcteurCreer->grille_class,
                profil: $profil,
                nom: $correcteurCreer->nom,
                echelles: new ArrayCollection()
            );

            foreach ($profil->echelles as $echelle) {

                $echelleCorrecteur = new EchelleCorrecteur(
                    id: 0, expression: "0", echelle: $echelle, correcteur: $correcteur
                );
                $echelleCorrecteur->echelle = $echelle;
                $echelleCorrecteur->id = 0;
                $echelleCorrecteur->expression = "0";

                $correcteur->echelles->add($echelleCorrecteur);
            }

            $entity_manager->persist($correcteur);
            $entity_manager->flush();

            return $this->redirectToRoute("correcteur_modifier", ["id" => $correcteur->id]);
        }

        return $this->render("correcteur/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ManagerRegistry           $doctrine,
        CorrecteurRepository      $correcteur_repository,
        CortestExpressionLanguage $cortest_expression_language,
        Request                   $request,
        int                       $id,
    ): Response
    {

        $correcteur = $correcteur_repository->find($id);

        $form = $this->createForm(CorrecteurType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("correcteur_consulter", ["id" => $id]);

        }

        $fonctions = $cortest_expression_language->getCortestFunctions();

        return $this->render("correcteur/modifier.html.twig",
            ["form" => $form->createView(), "fonctions" => $fonctions]);
    }

    #[Route("/ajoutEchelle/{id}/{idEchelle}", name: "ajoutEchelle")]
    public function ajoutEchelle(
        ManagerRegistry           $doctrine,
        CorrecteurRepository      $correcteur_repository,
        EchelleRepository         $echelleRepository,
        Request                   $request,
        int                       $id,
        int                       $idEchelle=Null,
    ): Response
    {

        $correcteur = $correcteur_repository->find($id);
        if (!is_null($idEchelle)){
            $echelle = $echelleRepository->find($idEchelle);
            $echelleCorrecteur = new EchelleCorrecteur(
                id: 0, expression: "0", echelle: $echelle, correcteur: $correcteur
            );
            $correcteur->echelles[] = $echelleCorrecteur;
            $doctrine->getManager()->flush();
        }

        $echelles = $echelleRepository->findByIdDifferent($correcteur->get_echelles_ids());

        return $this->render("correcteur/ajoutEchelle.html.twig",
            ["echellesDisponibles" => $echelles, "id" => $correcteur->id]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entity_manager,
        CorrecteurRepository   $correcteur_repository,
        int                    $id,
    ): Response
    {
        $correcteur = $correcteur_repository->find($id);

        if ($correcteur != null) {
            $entity_manager->remove($correcteur);

            foreach ($correcteur->echelles as $echelle) {
                $entity_manager->remove($echelle);
            }

            $entity_manager->flush();
        }

        return $this->redirectToRoute("correcteur_index");
    }

}