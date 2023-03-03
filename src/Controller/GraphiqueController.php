<?php

namespace App\Controller;

use App\Core\Renderer\RendererRepository;
use App\Entity\Graphique;
use App\Form\CreerGraphiqueType;
use App\Form\Data\GraphiqueCreer;
use App\Form\GraphiqueType;
use App\Repository\GraphiqueRepository;
use App\Form\GraphiqueBRMRType;
use App\Form\GraphiqueFooterType;
use App\Form\GraphiqueSubtestEchelleType;
use App\Form\GraphiqueSubtestsType;
use App\Repository\ProfilRepository;
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
            'arrayType' => array_flip(Graphique::TYPE_SUBTEST),
            'footerType' => array_flip(Graphique::TYPE_FOOTER),
            'echelleNoms' => array_flip($graphique->getArrayEchelle()),
            'echelleAffiche' => $graphique->getArrayEchelleAffiches(),
        ]);    }

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
                renderer_index: $creer_graphique->renderer_index
            );

            Graphique::initializeEchelles($graphique, $renderer);

            $entity_manager->persist($graphique);
            $entity_manager->flush();


            return $this->redirectToRoute("graphique_modifier", ["id" => $graphique->id]);
        }

        return $this->render("graphique/creer.html.twig", ["form" => $form]);
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

        return $this->render("graphique/modifier.html.twig", ["form" => $form]);
    }

    #[Route("/ajouter-subtests/{id}", name: "ajouter_subtests")]
    public function ajouterSubtest(
        Request             $request,
        GraphiqueRepository $graphique_repository,
        ManagerRegistry     $doctrine,
        int                 $id,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        $form = $this->createForm(GraphiqueSubtestsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputs = $form->getData();
            $graphique->subtests[] = array($inputs['Nouveau_Subtest'], $inputs['Type_Subtest'], array(), array());

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute("graphique_ajouter_composite", ["id" => $graphique->id, "idSub" => count($graphique->subtests)-1]);
        }

        return $this->render('graphique/ajouter_subtests.html.twig', [
            'form' => $form->createView(),
            'graphique' => $graphique,
            'arrayType' => array_flip(Graphique::TYPE_SUBTEST),
            'footerType' => array_flip(Graphique::TYPE_FOOTER),
            'echelleNoms' => array_flip($graphique->getArrayEchelle()),
            'echelleAffiche' => $graphique->getArrayEchelleAffiches(),
        ]);
    }

    #[Route("/supprimer-subtest/{id}/{idSub}", name: "supprimer_subtest")]
    public function supprimerSubtest(
        EntityManagerInterface $entity_manager,
        GraphiqueRepository    $graphique_repository,
        int                    $id,
        int                    $idSub,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if (array_key_exists($idSub, $graphique->subtests)) {
            array_splice($graphique->subtests, $idSub, 1);

            $entity_manager->flush();
        }

        return $this->redirectToRoute("graphique_ajouter_subtests", ['id' => $id]);
    }

    #[Route("/ajouter-composite/{id}/{idSub}", name: "ajouter_composite")]
    public function ajouterComposite(
        Request             $request,
        GraphiqueRepository $graphique_repository,
        ManagerRegistry     $doctrine,
        int                 $id,
        int                 $idSub,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if ($graphique->subtests[$idSub][1] == Graphique::SUBTEST_BRMR){
            $form = $this->createForm(GraphiqueBRMRType::class, options: ['choices' => $graphique->getArrayEchelle()]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $inputs = $form->getData();
                $graphique->subtests[$idSub][2][] = array($inputs['Nouvelle_Echelle'], array($inputs['Echelle_Bonnes_Reponses'], $inputs['Echelle_Mauvaises_Reponses']));

                $entityManager = $doctrine->getManager();
                $entityManager->flush();

                return $this->redirectToRoute("graphique_ajouter_composite", ["id" => $id, "idSub" => $idSub]);
            }
        }
        else {
            $form = $this->createForm(GraphiqueSubtestEchelleType::class, options: ['choices' => $graphique->getArrayEchelle()]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $graphique->subtests[$idSub][2][] = array($form->getData()['Nouvelle_Echelle'], array());

                $entityManager = $doctrine->getManager();
                $entityManager->flush();

                return $this->redirectToRoute("graphique_ajouter_simple", ["id" => $graphique->id, "idSub" => $idSub, "idComp" => count($graphique->subtests[$idSub][2])-1]);
            }
        }

        return $this->render('graphique/modifier_subtest.html.twig', [
            'form' => $form->createView(),
            'graphique' => $graphique,
            'idSubtest' => $idSub,
            'idComposite' => -1,
            'arrayType' => array_flip(Graphique::TYPE_SUBTEST),
            'footerType' => array_flip(Graphique::TYPE_FOOTER),
            'echelleNoms' => array_flip($graphique->getArrayEchelle()),
            'echelleAffiche' => $graphique->getArrayEchelleAffiches(),
        ]);
    }

    #[Route("/supprimer-composite/{id}/{idSub}/{idComp}", name: "supprimer_composite")]
    public function supprimerComposite(
        EntityManagerInterface $entity_manager,
        GraphiqueRepository    $graphique_repository,
        int                    $id,
        int                    $idSub,
        int                    $idComp,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if (array_key_exists($idComp, $graphique->subtests[$idSub][2])) {
            array_splice($graphique->subtests[$idSub][2], $idComp, 1);

            $entity_manager->flush();
        }

        return $this->redirectToRoute("graphique_ajouter_composite", ['id' => $id, 'idSub' => $idSub]);
    }

    #[Route("/ajouter-simple/{id}/{idSub}/{idComp}", name: "ajouter_simple")]
    public function ajouterSimple(
        Request             $request,
        GraphiqueRepository $graphique_repository,
        ManagerRegistry     $doctrine,
        int                 $id,
        int                 $idSub,
        int                 $idComp,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        $form = $this->createForm(GraphiqueSubtestEchelleType::class, options: ['choices' => $graphique->getArrayEchelle()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $graphique->subtests[$idSub][2][$idComp][1][] = $form->getData()['Nouvelle_Echelle'];

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute("graphique_ajouter_simple", ["id" => $graphique->id, "idSub" => $idSub, "idComp" => $idComp]);
        }

        return $this->render('graphique/modifier_subtest.html.twig', [
            'form' => $form->createView(),
            'graphique' => $graphique,
            'idSubtest' => $idSub,
            'idComposite' => $idComp,
            'arrayType' => array_flip(Graphique::TYPE_SUBTEST),
            'footerType' => array_flip(Graphique::TYPE_FOOTER),
            'echelleNoms' => array_flip($graphique->getArrayEchelle()),
            'echelleAffiche' => $graphique->getArrayEchelleAffiches(),
        ]);
    }

    #[Route("/ajouter-footer/{id}/{idSub}", name: "ajouter_footer")]
    public function ajouterFooter(
        Request             $request,
        GraphiqueRepository $graphique_repository,
        ManagerRegistry     $doctrine,
        int                 $id,
        int                 $idSub,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        $form = $this->createForm(GraphiqueFooterType::class, options: ['choices' => $graphique->getArrayEchelle()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputs = $form->getData();
            $graphique->subtests[$idSub][3][] = array($inputs['Nouveau_Bas_De_Cadre'], $inputs['Type_Bas_De_Cadre']);

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute("graphique_ajouter_footer", ["id" => $graphique->id, "idSub" => $idSub]);
        }

        return $this->render('graphique/modifier_subtest.html.twig', [
            'form' => $form->createView(),
            'graphique' => $graphique,
            'idSubtest' => $idSub,
            'arrayType' => array_flip(Graphique::TYPE_SUBTEST),
            'footerType' => array_flip(Graphique::TYPE_FOOTER),
            'echelleNoms' => array_flip($graphique->getArrayEchelle()),
            'echelleAffiche' => $graphique->getArrayEchelleAffiches(),
            'idComposite' => -1,
            'isFooter' => true,
        ]);
    }

    #[Route("/supprimer-footer/{id}/{idSub}/{idFoot}", name: "supprimer_footer")]
    public function supprimerFooter(
        EntityManagerInterface $entity_manager,
        GraphiqueRepository    $graphique_repository,
        int                    $id,
        int                    $idSub,
        int                    $idFoot,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if (array_key_exists($idFoot, $graphique->subtests[$idSub][3])) {
            array_splice($graphique->subtests[$idSub][3], $idFoot, 1);

            $entity_manager->flush();
        }

        return $this->redirectToRoute("graphique_ajouter_composite", ['id' => $id, 'idSub' => $idSub]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface $entity_manager,
        GraphiqueRepository    $graphique_repository,
        int                    $id,
    ): Response
    {
        $graphique = $graphique_repository->find($id);

        if ($graphique != null) {
            $entity_manager->remove($graphique);

            foreach ($graphique->echelles as $echelle) {
                $entity_manager->remove($echelle);
            }
            $entity_manager->flush();
        }

        return $this->redirectToRoute("graphique_index");
    }

}