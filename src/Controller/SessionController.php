<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Core\ReponseCandidat\ReponsesCandidatSessionStorage;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Entity\CortestLogEntry;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ConcoursRepository;
use App\Repository\GrilleRepository;
use App\Repository\SessionRepository;
use App\Repository\SgapRepository;
use App\Repository\TestRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session", name: "session_")]
class SessionController extends AbstractController
{

    #[Route('/index', name: "index")]
    public function index(
        SessionRepository $sessionRepository,
        GrilleRepository  $grilleRepository,
    ): Response
    {
        /** @var array $session */
        $sessions = $sessionRepository->findBy([], ['id' => 'desc']);

        $grilles = $grilleRepository->indexToInstance();

        return $this->render('session/index.html.twig', ["sessions" => $sessions, "grilles" => $grilles]);
    }

    #[Route('/creer', name: "creer")]
    public function creer(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        SgapRepository         $sgapRepository,
        TestRepository         $testRepository,
        ConcoursRepository     $concoursRepository,
        Request                $request): Response
    {
        $test = $testRepository->findOneBy([]);
        $sgap = $sgapRepository->findOneBy([]);
        $concours = $concoursRepository->findOneBy([]);

        if ($sgap == null) {
            $this->addFlash("warning", "Pas de SGAP disponible, veuillez en créer un.");
            return $this->redirectToRoute("sgap_index");
        }

        if ($test == null) {
            $this->addFlash("warning", "Pas de tests disponible, veuillez en créer un.");
            return $this->redirectToRoute("test_index");
        }

        if ($concours == null) {
            $this->addFlash("warning", "Pas de concours disponible, veuillez en créer un.");
            return $this->redirectToRoute("concours_index");
        }

        $session = new Session(
            id: 0,
            date: new DateTime("now"),
            numero_ordre: 0,
            observations: "",
            test: $test,
            sgap: $sgap,
            reponses_candidats: new ArrayCollection(),
            concours: $concours,
        );

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($session);
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $session,
                message: "Création d'une session par formulaire",
            );
            $entityManager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/creer.html.twig',
            ["form" => $form->createView()]
        );
    }

    #[Route('/modifier/{id}', name: "modifier")]
    public function modifier(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        Request                $request,
        Session                $session): Response
    {
        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $session,
                message: "Modification d'une session par formulaire",
            );
            $entityManager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form->createView()]
        );
    }

    #[Route('/consulter/{id}', name: "consulter")]
    public function consulter(Session $session): Response
    {
        return $this->render('session/session.html.twig', ["session" => $session]);
    }

    #[Route("/csv/{id}", name: "csv")]
    public function csv(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        Session                 $session
    ): Response
    {
        $reponsesCandidatStorage->setFromSession($session);
        return $this->redirectToRoute("csv_reponses");
    }

    #[Route("/csv_trie/{id}", name: "csv_trie")]
    public function csvTrie(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        Session                 $session
    ): Response
    {
        $reponsesCandidatStorage->setFromSession($session);
        return $this->redirectToRoute("csv_reponses_triees");
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger                 $activiteLogger,
        LoggerInterface                $logger,
        EntityManagerInterface         $entityManager,
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        Session                        $session): Response
    {
        $reponsesCandidatSessionStorage->set([]); // TODO être un peu plus précis, c'est très conservatif

        $logger->info("Suppression de la session : " . $session->id);

        $activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_SUPPRIMER,
            object: $session,
            message: "Suppression d'une session",
        );
        $entityManager->remove($session);
        $entityManager->flush();

        $this->addFlash("success", "La session a bien été supprimée.");

        return $this->redirectToRoute("session_index");
    }
}