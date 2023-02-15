<?php

namespace App\Controller;

use App\Core\Renderer\RendererRepository;
use App\Entity\Graphique;
use App\Form\CreerGraphiqueType;
use App\Form\Data\GraphiqueCreer;
use App\Form\GraphiqueType;
use App\Repository\GraphiqueRepository;
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

        return $this->render("graphique/graphique.html.twig", ["graphique" => $graphique, "renderer" => $renderer]);
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
            $entity_manager->flush();
            $this->addFlash("success", "Le graphique a bien été supprimé");
        }

        return $this->redirectToRoute("graphique_index");
    }

}