<?php

namespace App\Controller;

use App\Core\Renderer\RendererRepository;
use App\Entity\Graphique;
use App\Entity\Subtest;
use App\Form\CreerGraphiqueType;
use App\Form\Data\EchelleGraphiqueChoice;
use App\Form\Data\EchelleSubtestBrMr;
use App\Form\Data\GraphiqueCreer;
use App\Form\EchelleGraphiqueChoiceType;
use App\Form\EchelleSubtestBrMrType;
use App\Form\EchelleSubtestFooter;
use App\Form\GraphiqueType;
use App\Form\SubtestNomType;
use App\Form\SubtestType;
use App\Repository\GraphiqueRepository;
use App\Repository\ProfilRepository;
use App\Repository\SubtestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/graphique", name: "graphique_")]
class GraphiqueController extends AbstractController
{

    #[Route("/index", name: 'index')]
    public function index(
        GraphiqueRepository $graphiqueRepository
    ): Response
    {
        $graphiques = $graphiqueRepository->findAll();

        return $this->render('graphique/index.html.twig',
            ["graphiques" => $graphiques]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        ProfilRepository       $profilRepository,
        RendererRepository     $rendererRepository,
        Request                $request
    ): Response
    {
        $profils = $profilRepository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créez un");
            return $this->redirectToRoute("profil_index");
        }

        $creer_graphique = new GraphiqueCreer(
            nom: "",
            renderer_index: $rendererRepository->sampleIndex(),
            profil: $profils[0]
        );

        $form = $this->createForm(CreerGraphiqueType::class, $creer_graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil = $creer_graphique->profil;
            $renderer = $rendererRepository->fromIndex($creer_graphique->renderer_index);

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

            $entityManager->persist($graphique);
            $entityManager->flush();


            return $this->redirectToRoute("graphique_modifier", ["id" => $graphique->id]);
        }

        return $this->render("graphique/creer.html.twig", ["form" => $form]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        RendererRepository $rendererRepository,
        Graphique          $graphique
    ): Response
    {
        $renderer = $rendererRepository->fromIndex($graphique->renderer_index);

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
        RendererRepository $rendererRepository,
        ManagerRegistry    $doctrine,
        Request            $request,
        Graphique          $graphique
    ): Response
    {
        $renderer = $rendererRepository->fromIndex($graphique->renderer_index);

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
        EntityManagerInterface $entityManager,
        Graphique              $graphique
    ): Response
    {
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

            $entityManager->persist($subtest);
            $entityManager->flush();

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
        Request                               $request,
        EntityManagerInterface                $entityManager,
        #[MapEntity(id: "idSubtest")] Subtest $subtest
    ): Response
    {
        $form = $this->createForm(SubtestNomType::class, $subtest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($subtest);
            $entityManager->flush();

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
        LoggerInterface                        $logger,
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest
    ): Response
    {
        $graphiqueId = $subtest->graphique->id;

        $logger->info("Suppression du subtest " . $subtest->id);
        $entityManager->remove($subtest);
        $entityManager->flush();

        $this->addFlash("success", "Subtest supprimé");

        return $this->redirectToRoute("graphique_consulter", ["id" => $graphiqueId]);
    }

    #[Route("/consulter-subtest/{id_subtest}", name: "consulter_subtest")]
    public function consulterSubtest(
        #[MapEntity(id: "id_subtest")] Subtest $subtest
    ): Response
    {
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
        Request                                $request,
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest
    ): Response
    {

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

                $entityManager->flush();

                return $this->redirectToRoute("graphique_consulter_subtest",
                    ["id_subtest" => $subtest->id]);
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

                $entityManager->flush();

                return $this->redirectToRoute("graphique_consulter_subtest",
                    ["id_subtest" => $subtest->id]);
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
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest,
        int                                    $id_composite,
    ): Response
    {
        $subtest->echelles_core = array_filter(
            $subtest->echelles_core,
            function (array $id_composite_et_ids_simples) use ($id_composite) {

                return $id_composite_et_ids_simples[0] != $id_composite;
            }
        );

        $entityManager->flush();

        return $this->redirectToRoute("graphique_consulter_subtest", ["id_subtest" => $subtest->id]);
    }

    #[Route("/ajouter-simple/{id_subtest}/{id_composite}", name: "ajouter_simple")]
    public function ajouterSimple(
        Request                                $request,
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest,
        int                                    $id_composite,
    ): Response
    {

        if (empty($subtest->graphique->echellesSimples())) {
            $this->addFlash("warning", "Le graphique n'a pas d'échelles simples");
            return $this->redirectToRoute("graphique_consulter", ["id" => $subtest->graphique->id]);
        }

        $echelleGraphiqueChoice = new EchelleGraphiqueChoice(
            $subtest->graphique->echellesSimples()[0]
        );

        $form = $this->createForm(
            EchelleGraphiqueChoiceType::class,
            $echelleGraphiqueChoice,
            options: ["echelles" => $subtest->graphique->echellesSimples()]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var array $echellesComposites */
            foreach ($subtest->echelles_core as &$echellesComposites) {
                if (count($echellesComposites) == 2 and $echellesComposites[0] == $id_composite) {
                    $echellesComposites[1][] = $echelleGraphiqueChoice->echelle->id;
                    break;
                }
            }

            $entityManager->flush();

            $this->addFlash("info", "Echelle ajoutée avec succès");
            return $this->redirectToRoute("graphique_consulter_subtest",
                ["id_subtest" => $subtest->id]);
        }

        return $this->render("graphique/subtest_form.twig", [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Ajouter une échelle simple"
        ]);
    }

    #[Route("/ajouter-footer/{id_subtest}", name: "ajouter_footer")]
    public function ajouterFooter(
        Request                                $request,
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest
    ): Response
    {
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
            $entityManager->flush();

            return $this->redirectToRoute("graphique_consulter_subtest", ["id_subtest" => $subtest->id]);
        }

        return $this->render('graphique/subtest_form.twig', [
            "form" => $form->createView(),
            "subtest" => $subtest,
            "titre_form" => "Ajouter une échelle de bas de page"
        ]);
    }

    #[Route("/supprimer-footer/{id_subtest}/{id_footer}", name: "supprimer_footer")]
    public function supprimerFooter(
        EntityManagerInterface                 $entityManager,
        #[MapEntity(id: "id_subtest")] Subtest $subtest,
        int                                    $id_footer,
    ): Response
    {

        $subtest->echelles_footer = array_filter(
            $subtest->echelles_footer,
            fn(array $echelle_id_and_type) => $echelle_id_and_type[0] != $id_footer
        );

        $entityManager->flush();

        return $this->redirectToRoute("graphique_consulter_subtest",
            ["id_subtest" => $subtest->id]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Graphique              $graphique
    ): Response
    {
        $logger->info("Suppression du graphique " . $graphique->id);

        $entityManager->remove($graphique);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du graphique enregistrée");

        return $this->redirectToRoute("graphique_index");
    }

}