<?php

namespace App\Controller;

use App\Core\Res\ProfilOuScore\ProfilOuScoreRepository;
use App\Entity\Etalonnage;
use App\Form\EtalonnageRowType;
use App\Form\EtalonnageType;
use App\Repository\EtalonnageRepository;
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

#[Route("/etalonnage", name: "etalonnage_")]
class EtalonnageController extends AbstractController
{
    #[Route("/consulter-liste", name: 'consulter_liste')]
    public function consulterEtalonnages(
        EtalonnageRepository    $etalonnage_repository,
        ProfilOuScoreRepository $profil_ou_score_repository
    ): Response
    {
        $etalonnages = $etalonnage_repository->findAll();

        $scores = [];

        foreach ($etalonnages as $etalonnage) {
            $scores[$etalonnage->id] = $profil_ou_score_repository->get($etalonnage->score_id)->getNom();
        }

        return $this->render('etalonnage/index.html.twig',
            ["etalonnages" => $etalonnages, "scores" => $scores]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulterEtalonnage(
        EtalonnageRepository    $etalonnage_repository,
        ProfilOuScoreRepository $profil_ou_score_repository,
        int                     $id
    ): Response
    {
        $etalonnage = $etalonnage_repository->find($id);
        $score = $profil_ou_score_repository->get($etalonnage->score_id);
        return $this->render("etalonnage/etalonnage.html.twig", ["etalonnage" => $etalonnage, "score" => $score]);
    }

    #[Route("/creer", name: "creer")]
    public function creerEtalonnage(
        ProfilOuScoreRepository $profil_ou_score_repository,
        Request                 $request
    ): Response
    {
        $form = $this->createFormBuilder()
            ->add("profil_ou_score_id", ChoiceType::class, [
                "choices" => $profil_ou_score_repository->nomToIndex()
            ])
            ->add("nombre_classes", IntegerType::class, ["label" => "Nombre de classes"])
            ->add("submit", SubmitType::class, ["label" => "Valider"])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil_ou_score_id = $form->getData()["profil_ou_score_id"];
            $nombre_classes = $form->getData()["nombre_classes"];

            return $this->redirectToRoute("etalonnage_creer_avec_profil_ou_score",
                ["profil_ou_score_id" => $profil_ou_score_id, "nombre_classes" => $nombre_classes]);
        }

        return $this->render("etalonnage/creer_choisir_grille_et_score.html.twig", ["form" => $form]);
    }

    #[Route("/creer-avec-profil-ou-score/{profil_ou_score_id}/{nombre_classes}", name: "creer_avec_profil_ou_score")]
    public function creerEtalonnageAvecProfilOuScore(
        ManagerRegistry         $doctrine,
        ProfilOuScoreRepository $profil_ou_score_repository,
        Request                 $request,
        int                     $nombre_classes,
        int                     $profil_ou_score_id
    )
    {
        $profil_ou_score = $profil_ou_score_repository->get($profil_ou_score_id);

        $etalonnage = new Etalonnage(
            id: 0,
            score_id: $profil_ou_score_id,
            nom: "",
            nombre_classes: $nombre_classes,
            values: $profil_ou_score->generateEtalonnageValues($nombre_classes)
        );

        $form = $this->createForm(EtalonnageType::class, $etalonnage);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->persist($etalonnage);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("etalonnage_consulter", ["id" => $etalonnage->id]);

        }

        return $this->render("etalonnage/form.html.twig", ["form" => $form]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ManagerRegistry      $doctrine,
        EtalonnageRepository $etalonnage_repository,
        int                  $id,
    )
    {
        $etalonnage = $etalonnage_repository->find($id);

        if($etalonnage != null) {
            $doctrine->getManager()->remove($etalonnage);
            $doctrine->getManager()->flush();
        }

        return $this->redirectToRoute("etalonnage_consulter_liste");
    }

}