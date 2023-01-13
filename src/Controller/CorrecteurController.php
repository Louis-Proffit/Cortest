<?php

namespace App\Controller;

use App\Core\Res\Grille\GrilleRepository;
use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\Correcteur;
use App\Form\CorrecteurType;
use App\Repository\CorrecteurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/correcteur", name: "correcteur_")]
class CorrecteurController extends AbstractController
{
    #[Route("/consulter-liste", name: 'consulter_liste')]
    public function consulterCorrecteurs(
        CorrecteurRepository    $correcteur_repository,
        ProfilOuScoreRepository $profil_ou_score_repository
    ): Response
    {
        $correcteurs = $correcteur_repository->findAll();

        $grilles = [];
        $scores = [];

        foreach ($correcteurs as $correcteur) {
            $scores[$correcteur->id] = $profil_ou_score_repository->get($correcteur->score_id)->getNom();
            $grilles[$correcteur->id] = new ($correcteur->grilleClass)();
        }

        return $this->render('correcteur/index.html.twig',
            ["correcteurs" => $correcteurs, "scores" => $scores, "grilles" => $grilles]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulterCorrecteur(
        CorrecteurRepository    $correcteur_repository,
        ProfilOuScoreRepository $profil_ou_score_repository,
        int                     $id
    ): Response
    {
        $correcteur = $correcteur_repository->find($id);
        $grille = new ($correcteur->grilleClass)();
        $score = $profil_ou_score_repository->get($correcteur->score_id);
        return $this->render("correcteur/correcteur.html.twig",
            ["correcteur" => $correcteur, "score" => $score, "grille" => $grille]);
    }

    #[
        Route("/creer", name: "creer")]
    public function creerCorrecteur(
        ProfilOuScoreRepository $profil_ou_score_repository,
        GrilleRepository        $grille_repository,
        Request                 $request
    ): Response
    {
        $form = $this->createFormBuilder()
            ->add("grilleClass", ChoiceType::class, [
                "choices" => $grille_repository->nomToClassName(),
                "label" => "Grille (entrée)"
            ])
            ->add("profil_ou_score_id", ChoiceType::class, [
                "choices" => $profil_ou_score_repository->nomToIndex(),
                "label" => "Profil (sortie)"
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil_ou_score_id = $form->getData()["profil_ou_score_id"];
            $grilleClass = $form->getData()["grilleClass"];

            return $this->redirectToRoute("correcteur_creer_avec_grille_et_score",
                ["score_id" => $profil_ou_score_id, "grilleClass" => $grilleClass]);
        }

        return $this->render("correcteur/creer_choisir_grille_et_score.html.twig", ["form" => $form]);
    }

    #[Route("/creer-avec-grille-et-score/{score_id}/{grilleClass}", name: "creer_avec_grille_et_score")]
    public function creerCorrecteurAvecProfilOuScore(
        ManagerRegistry         $doctrine,
        ProfilOuScoreRepository $profil_ou_score_repository,
        Request                 $request,
        int                     $score_id,
        string                  $grilleClass
    )
    {
        $profil_ou_score = $profil_ou_score_repository->get($score_id);

        $correcteur = new Correcteur(
            id: 0,
            grilleClass: $grilleClass,
            score_id: $score_id,
            nom: "",
            values: $profil_ou_score->generateCorrecteurValues()
        );

        $form = $this->createForm(CorrecteurType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->persist($correcteur);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("correcteur_consulter_liste");

        }

        return $this->render("correcteur/form.html.twig", ["form" => $form]);
    }

    /*
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ManagerRegistry      $doctrine,
        CorrecteurRepository $correcteur_repository,
        Request              $request,
        int                  $id)
    {
        $correcteur = $correcteur_repository->find($id);

        $form = $this->createForm(CorrecteurType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->persist($correcteur);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("correcteur_consulter_liste");

        }

        return $this->render("correcteur/form.html.twig", ["form" => $form]);
    }*/

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ManagerRegistry      $doctrine,
        CorrecteurRepository $correcteur_repository,
        int                  $id,
    )
    {
        $correcteur = $correcteur_repository->find($id);

        if ($correcteur != null) {
            $doctrine->getManager()->remove($correcteur);
            $doctrine->getManager()->flush();
        }

        return $this->redirectToRoute("correcteur_consulter_liste");
    }

}