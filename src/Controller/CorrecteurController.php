<?php

namespace App\Controller;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Import\ImportCorrecteurXML;
use App\Core\Import\ImportCorrecteurXMLErrorHandler;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Form\CorrecteurCreerType;
use App\Form\CorrecteurType;
use App\Form\Data\CorrecteurCreer;
use App\Form\ImportCorrecteurType;
use App\Repository\ConcoursRepository;
use App\Repository\CorrecteurRepository;
use App\Repository\GrilleRepository;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/correcteur", name: "correcteur_")]
class CorrecteurController extends AbstractController
{
    #[Route("/index", name: 'index')]
    public function index(
        CorrecteurRepository $correcteur_repository,
        GrilleRepository     $grille_repository
    ): Response
    {
        $correcteurs = $correcteur_repository->findAll();

        $grilles = $grille_repository->indexToInstance();

        return $this->render('correcteur/index.html.twig',
            ["correcteurs" => $correcteurs, "grilles" => $grilles]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        CorrecteurRepository $correcteur_repository,
        GrilleRepository     $grille_repository,
        int                  $id
    ): Response
    {
        $correcteur = $correcteur_repository->find($id);
        $grille = $grille_repository->getFromIndex($correcteur->concours->index_grille);
        return $this->render("correcteur/correcteur.html.twig",
            ["correcteur" => $correcteur, "grille" => $grille]);
    }

    #[Route("/importer", name: "importer")]
    public function importer(
        EntityManagerInterface $entityManager,
        Session                $session,
        ImportCorrecteurXML    $importCorrecteurXML,
        Request                $request
    ): Response
    {
        $form = $this->createForm(ImportCorrecteurType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get(ImportCorrecteurType::FILE_KEY)->getData();

            $correcteur = $importCorrecteurXML->load(new ImportCorrecteurXMLErrorHandler($session), $file->getContent());

            if ($correcteur) {
                $entityManager->persist($correcteur);
                $entityManager->flush();

                $this->addFlash("success", "Correcteur importé");

                return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);
            }
        }

        return $this->render("correcteur/form_importer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        ConcoursRepository     $concours_repository,
        ProfilRepository       $profil_repository,
        Request                $request
    ): Response
    {
        $profils = $profil_repository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créer un.");
            return $this->redirectToRoute("profil_index");
        }

        $all_concours = $concours_repository->findAll();

        if (empty($all_concours)) {
            $this->addFlash("warning", "Pas de concours disponible, veuillez en créer un.");
            return $this->redirectToRoute("concours_index");
        }

        $correcteurCreer = new CorrecteurCreer(
            profil: $profils[0],
            concours: $all_concours[0],
            nom: ""
        );
        $form = $this->createForm(CorrecteurCreerType::class, $correcteurCreer);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil = $correcteurCreer->profil;
            $concours = $correcteurCreer->concours;

            $correcteur = new Correcteur(
                id: 0,
                concours: $concours,
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