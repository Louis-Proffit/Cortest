<?php

namespace App\Controller;

use App\Core\Renderer\RendererRepository;
use App\Entity\Graphique;
use App\Entity\Subtest;
use App\Form\CreerGraphiqueType;
use App\Form\Data\EchelleSubtestBrMr;
use App\Form\Data\EchelleGraphiqueChoice;
use App\Form\Data\GraphiqueCreer;
use App\Form\GraphiqueType;
use App\Form\SubtestNomType;
use App\Repository\GraphiqueRepository;
use App\Form\EchelleSubtestBrMrType;
use App\Form\EchelleSubtestFooter;
use App\Form\EchelleGraphiqueChoiceType;
use App\Form\SubtestType;
use App\Repository\ProfilRepository;
use App\Repository\SubtestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/graphique", name: "graphique_")]
class GraphiqueController extends AbstractController
{

    #[Route("/index", name: 'index')]
    public function index(
        GraphiqueRepository $graphique_repository
    ): Response
    {
        $graphiques = $graphique_repository->findAll();

        return $this->render('graphique/index.html.twig',
            ["graphiques" => $graphiques]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        ProfilRepository       $profil_repository,
        RendererRepository     $renderer_repository,
        Request                $request
    ): Response
    {
        $profils = $profil_repository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, créez en un");
            return $this->redirectToRoute("profil_index");
        }

        $creer_graphique = new GraphiqueCreer(
            nom: "",
            renderer_index: $renderer_repository->sampleIndex(),
            profil: $profils[0]
        );

        $form = $this->createForm(CreerGraphiqueType::class, $creer_graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil = $creer_graphique->profil;
            $renderer = $renderer_repository->fromIndex($creer_graphique->renderer_index);

            $graphique = new Graphique(
                id: 0,
                options: $renderer->initializeOptions(),
                profil: $profil,
                echelles: new ArrayCollection(),
                nom: $creer_graphique->nom,
                renderer_index: $creer_graphique->renderer_index,
                subtests: new ArrayCollection()
            );

            Graphique::initializeEchelles($graphique, $renderer);

            $entity_manager->persist($graphique);
            $entity_manager->flush();


            return $this->redirectToRoute("graphique_modifier", ["id" => $graphique->id]);
        }

        return $this->render("graphique/creer.html.twig", ["form" => $form]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        GraphiqueRepository $graphique_repository,
        RendererRepository  $renderer_repository,
        int                 $id
    ): Response
    {
        $graphique = $graphique_repository->find($id);
        $renderer = $renderer_repository->fromIndex($graphique->renderer_index);

        return $this->render("graphique/graphique.html.twig", ["graphique" => $graphique,
            "renderer" => $renderer,
            'arrayType' => array_flip(Subtest::TYPES_SUBTEST_CHOICES),
            'footerType' => array_flip(Subtest::TYPES_FOOTER_CHOICES),
            'echelleNoms' => array_flip($graphique->getEchellesNomToNomPhp()),
            'echelleAffiche' => $graphique->getEchellesNomPhpToNomAffiche(),
        ]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        GraphiqueRepository $graphique_repository,
        RendererRepository  $renderer_repository,
        ManagerRegistry     $doctrine,
        Request             $request,
        int                 $id,
    ): Response
    {
        $graphique = $graphique_repository->find($id);
        $renderer = $renderer_repository->fromIndex($graphique->renderer_index);

        $form = $this->createForm(GraphiqueType::class, $graphique, [GraphiqueType::OPTION_RENDERER => $renderer]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("graphique_consulter", ["id" => $graphique->id]);
        }

        return $this->render("graphique/modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/creer-subtest/{id}", name: "creer_subtest")]
    public function ajouterSubtest(
        Request                $request,
        GraphiqueRepository    $graphique_repository,
        EntityManagerInterface $entity_manager,
        int                    $id,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if ($graphique == null) {
            $this->addFlash("warning", "Le graphique n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $subtest = new Subtest(
            0,
            "",
            Subtest::TYPE_SUBTEST_BR_MR,
            array(),
            array(),
            $graphique
        );

        $form = $this->createForm(SubtestType::class, $subtest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entity_manager->persist($subtest);
            $entity_manager->flush();

            return $this->redirectToRoute("graphique_consulter_subtest",
                ["id_subtest" => $subtest->id]);
        }

        return $this->render('graphique/subtest_form.twig', [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Créer un subtest"
        ]);
    }

    #[Route("/modifier-nom-subtest/{id}/{idSubtest}", name: "modifier_nom_subtest")]
    public function modifierNomSubtest(
        Request                $request,
        GraphiqueRepository    $graphique_repository,
        SubtestRepository      $subtestRepository,
        EntityManagerInterface $entity_manager,
        int                    $id,
        int                    $idSubtest,
    ): Response
    {
        $subtest = $subtestRepository->find($idSubtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $form = $this->createForm(SubtestNomType::class, $subtest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entity_manager->persist($subtest);
            $entity_manager->flush();

            return $this->redirectToRoute("graphique_consulter_subtest",
                ["id_subtest" => $subtest->id]);
        }

        return $this->render('graphique/subtest_form.twig', [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Modifier le nom du subtest"
        ]);
    }

    #[Route("/supprimer-subtest/{id_subtest}", name: "supprimer_subtest")]
    public function supprimerSubtest(
        EntityManagerInterface $entity_manager,
        SubtestRepository      $subtest_repository,
        int                    $id_subtest,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique_id = $subtest->graphique->id;
        $entity_manager->remove($subtest);
        $entity_manager->flush();

        return $this->redirectToRoute("graphique_consulter", ["id" => $graphique_id]);
    }

    #[Route("/consulter-subtest/{id_subtest}", name: "consulter_subtest")]
    public function consulterSubtest(
        SubtestRepository $subtest_repository,
        int               $id_subtest,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        if ($subtest->type == Subtest::TYPE_SUBTEST_BR_MR) {

            return $this->render("graphique/consulter_subtest_br_mr.html.twig",
                ["subtest" => $subtest]);
        } else {
            return $this->render("graphique/consulter_subtest_composite.html.twig",
                ["subtest" => $subtest]);
        }
    }


    #[Route("/ajouter-echelle-subtest/{id_subtest}", name: "subtest_ajouter_echelle")]
    public function modifierSubtest(
        Request                $request,
        SubtestRepository      $subtest_repository,
        EntityManagerInterface $entity_manager,
        int                    $id_subtest,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique = $subtest->graphique;

        if ($graphique->echelles->isEmpty()) {
            $this->addFlash("warning", "Le graphique n'a pas d'échelles, impossible");
            return $this->redirectToRoute("graphique_consulter", ["id" => $graphique->id]);
        }

        if ($subtest->type == Subtest::TYPE_SUBTEST_BR_MR) {

            $echelle_subtest_br_mr = new EchelleSubtestBrMr(
                $graphique->echelles[0],
                $graphique->echelles[0],
            );

            $form = $this->createForm(
                EchelleSubtestBrMrType::class,
                $echelle_subtest_br_mr,
                options: ["echelles" => $graphique->echelles->toArray()]
            );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $subtest->echelles_core[] = array(
                    $echelle_subtest_br_mr->echelle_br->echelle->id,
                    $echelle_subtest_br_mr->echelle_mr->echelle->id,
                );

                $entity_manager->flush();

                return $this->redirectToRoute("graphique_consulter_subtest",
                    ["id_subtest" => $id_subtest]);
            }
        } else {
            $echelle_subtest_simple = new EchelleGraphiqueChoice($graphique->echelles[0]);

            $form = $this->createForm(
                EchelleGraphiqueChoiceType::class,
                $echelle_subtest_simple,
                options: ["echelles" => $graphique->echellesComposite()]
            );

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $subtest->echelles_core[] = array($echelle_subtest_simple->echelle->id, array());

                $entity_manager->flush();

                return $this->redirectToRoute("graphique_consulter_subtest",
                    ["id_subtest" => $id_subtest]);
            }
        }

        return $this->render('graphique/subtest_form.twig', [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Ajouter une échelle au subtest"
        ]);
    }

    #[Route("/supprimer-composite/{id_subtest}/{id_composite}", name: "subtest_supprimer_composite")]
    public function supprimerComposite(
        EntityManagerInterface $entity_manager,
        SubtestRepository      $subtest_repository,
        int                    $id_subtest,
        int                    $id_composite,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $subtest->echelles_core = array_filter(
            $subtest->echelles_core,
            function (array $id_composite_et_ids_simples) use ($id_composite) {

                return $id_composite_et_ids_simples[0] != $id_composite;
            }
        );

        $entity_manager->flush();

        return $this->redirectToRoute("graphique_consulter_subtest", ["id_subtest" => $id_subtest]);
    }

    #[Route("/ajouter-simple/{id_subtest}/{id_composite}", name: "ajouter_simple")]
    public function ajouterSimple(
        Request                $request,
        SubtestRepository      $subtest_repository,
        EntityManagerInterface $entity_manager,
        int                    $id_subtest,
        int                    $id_composite,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        if (empty($subtest->graphique->echellesSimples())) {
            $this->addFlash("warning", "Le graphique n'a pas d'échelles simples");
            return $this->redirectToRoute("graphique_consulter", ["id" => $subtest->graphique->id]);
        }

        $echelle_graphique_choice = new EchelleGraphiqueChoice(
            $subtest->graphique->echellesSimples()[0]
        );

        $form = $this->createForm(
            EchelleGraphiqueChoiceType::class,
            $echelle_graphique_choice,
            options: ["echelles" => $subtest->graphique->echellesSimples()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var array $echelles_composites */
            foreach ($subtest->echelles_core as &$echelles_composites) {
                if (count($echelles_composites) == 2 and $echelles_composites[0] == $id_composite) {
                    $echelles_composites[1][] = $echelle_graphique_choice->echelle->id;
                    break;
                }
            }

            $entity_manager->flush();

            $this->addFlash("info", "Echelle ajoutée avec succès");
            return $this->redirectToRoute("graphique_consulter_subtest",
                ["id_subtest" => $id_subtest]);
        }

        return $this->render("graphique/subtest_form.twig", [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Ajouter une échelle simple"
        ]);
    }

    #[Route("/ajouter-footer/{id_subtest}", name: "ajouter_footer")]
    public function ajouterFooter(
        Request                $request,
        SubtestRepository      $subtest_repository,
        EntityManagerInterface $entity_manager,
        int                    $id_subtest,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $graphique = $subtest->graphique;
        if ($graphique->echelles->isEmpty()) {
            $this->addFlash("warning", "Pas d'échelles dans le graphique");
            return $this->redirectToRoute("graphique_index", ["id" => $graphique->id]);
        }

        $echelle_subtest_footer = new \App\Form\Data\EchelleSubtestFooter(
            $graphique->echelles[0],
            Subtest::TYPE_FOOTER_SCORE_AND_CLASSE
        );

        $form = $this->createForm(
            EchelleSubtestFooter::class,
            $echelle_subtest_footer,
            options: ["echelles" => $graphique->echelles->toArray()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $subtest->echelles_footer[] = array($echelle_subtest_footer->echelle->id, $echelle_subtest_footer->type);
            $entity_manager->flush();

            return $this->redirectToRoute("graphique_consulter_subtest", ["id_subtest" => $id_subtest]);
        }

        return $this->render('graphique/subtest_form.twig', [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Ajouter une échelle de bas de page"
        ]);
    }

    #[Route("/supprimer-footer/{id_subtest}/{id_footer}", name: "supprimer_footer")]
    public function supprimerFooter(
        EntityManagerInterface $entity_manager,
        SubtestRepository      $subtest_repository,
        int                    $id_subtest,
        int                    $id_footer,
    ): Response
    {
        $subtest = $subtest_repository->find($id_subtest);

        if ($subtest == null) {
            $this->addFlash("warning", "Le subtest n'existe pas");
            return $this->redirectToRoute("graphique_index");
        }

        $subtest->echelles_footer = array_filter(
            $subtest->echelles_footer,
            fn(array $echelle_id_and_type) => $echelle_id_and_type[0] != $id_footer
        );

        $entity_manager->flush();

        return $this->redirectToRoute("graphique_consulter_subtest",
            ["id_subtest" => $id_subtest]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entity_manager,
        GraphiqueRepository    $graphique_repository,
        int                    $id,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        $entity_manager->remove($graphique);
        $entity_manager->flush();

        return $this->redirectToRoute("graphique_index");
    }

}