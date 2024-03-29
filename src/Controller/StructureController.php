<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Entity\CortestLogEntry;
use App\Entity\Echelle;
use App\Entity\Structure;
use App\Form\CreerStructureType;
use App\Form\StructureType;
use App\Repository\StructureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/structure", name: "structure_")]
class StructureController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        StructureRepository $structureRepository
    ): Response
    {
        $structures = $structureRepository->findAll();

        return $this->render("structure/index.html.twig", ["structures" => $structures]);
    }

    #[Route("/consulter/{id}", name: "consulter")]
    public function consulter(
        Structure $structure
    ): Response
    {
        return $this->render("structure/structure.html.twig", ["structure" => $structure]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        $structure = new Structure(
            id: 0,
            nom: "",
            echelles: new ArrayCollection(),
            etalonnages: new ArrayCollection(),
            graphiques: new ArrayCollection()
        );

        $form = $this->createForm(CreerStructureType::class, $structure);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $echelleCount = $form[CreerStructureType::ECHELLES_COUNT_KEY]->getData();

            for ($i = 1; $i <= $echelleCount; $i++) {
                $echelle = new Echelle(
                    id: 0,
                    nom: "Echelle " . $i . " (A MODIFIER)",
                    nom_php: "echelle_" . $i . "_a_modifier",
                    type: Echelle::TYPE_ECHELLE_SIMPLE,
                    echelles_correcteur: new ArrayCollection(),
                    echelles_etalonnage: new ArrayCollection(),
                    structure: $structure
                );

                $structure->echelles->add($echelle);
            }

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $structure,
                message: "Création d'une structure par formulaire"
            );
            $entityManager->persist($structure);
            $entityManager->flush();

            return $this->redirectToRoute("structure_index");

        }

        return $this->render("structure/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        Request                $request,
        Structure              $structure
    ): RedirectResponse|Response
    {
        $form = $this->createForm(StructureType::class, $structure);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $structure,
                message: "Modification d'une structure par formulaire"
            );
            $entityManager->flush();

            return $this->redirectToRoute("structure_index");
        }

        return $this->render("structure/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/confirmer/{id}", name: "supprimer_confirmer")]
    public function supprimerConfirmer(Structure $structure): Response
    {
        $supprimable = Structure::supprimable($structure);
        return $this->render("structure/supprimer.html.twig", ["structure" => $structure, "supprimable" => $supprimable]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger         $activiteLogger,
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Structure              $structure): RedirectResponse
    {
        $supprimable = Structure::supprimable($structure);
        if ($supprimable) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_SUPPRIMER,
                object: $structure,
                message: "Suppression d'une structure"
            );
            $entityManager->remove($structure);
            $entityManager->flush();

            $this->addFlash("success", "La structure a bien été supprimée");

            return $this->redirectToRoute("structure_index");
        } else {
            $logger->info("Impossible de supprimer la structure", ["structure" => $structure]);
            $this->addFlash("danger", "Impossible de supprimer la structure");

            return $this->redirectToRoute("structure_supprimer_confirmer", ["id" => $structure->id]);
        }


    }
}