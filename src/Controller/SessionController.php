<?php

namespace App\Controller;

use App\Core\Grille\GrilleRepository;
use App\Core\Grille\Values\GrilleOctobre2019;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ConcoursRepository;
use App\Repository\SessionRepository;
use App\Repository\SgapRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session", name: "session_")]
class SessionController extends AbstractController
{

    #[Route('/index', name: "index")]
    public function index(
        SessionRepository $session_repository,
        GrilleRepository  $grille_repository,
    ): Response
    {
        /** @var array $session */
        $sessions = $session_repository->findAll();
        $grilles = $grille_repository->classNameToNom();

        return $this->render('session/index.html.twig', ["sessions" => $sessions, "grilles" => $grilles]);
    }

    #[Route('/creer', name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        SgapRepository         $sgap_repository,
        ConcoursRepository     $concours_repository,
        Request                $request): Response
    {
        $sgaps = $sgap_repository->findAll();

        if (empty($sgaps)) {
            $this->addFlash("warning", "Pas de sgap disponible, veuillez en créer un.");
            return $this->redirectToRoute("sgap_index");
        }

        $session = new Session(
            id: 0,
            date: new DateTime("now"),
            grille_class: GrilleOctobre2019::class,
            numero_ordre: 0,
            observations: "",
            concours: $concours_repository->findOneBy([]),
            type_concours: 0,
            version_batterie: 0,
            sgap: $sgaps[0],
            reponses_candidats: new ArrayCollection()
        );

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($session);
            $entity_manager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form]
        );
    }

    #[Route('/modifier/{id}', name: "modifier")]
    public function modifier(
        EntityManagerInterface $entity_manager,
        SessionRepository      $session_repository,
        Request                $request,
        int                    $id): Response
    {
        $session = $session_repository->find($id);

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form]
        );
    }

    #[Route('/consulter/{id}', name: "consulter")]
    public function consulter(SessionRepository $session_repository, int $id): Response
    {
        $session = $session_repository->find($id);

        return $this->render(
            'session/session.html.twig',
            ["session" => $session]
        );
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(ManagerRegistry $doctrine,
                              SessionRepository $session_repository,
                              int $id): Response
    {

        $session = $session_repository->find($id);

        if ($session != null) {

            foreach ($session->reponses_candidats->toArray() as $candidat_reponse) {
                $doctrine->getManager()->remove($candidat_reponse);
            }

            $doctrine->getManager()->remove($session);
            $doctrine->getManager()->flush();
        }


        return $this->redirectToRoute("session_index");
    }

}