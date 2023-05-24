<?php

namespace App\Controller;

use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ConcoursRepository;
use App\Repository\GrilleRepository;
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
        SessionRepository $sessionRepository,
        GrilleRepository  $grilleRepository,
    ): Response
    {
        /** @var array $session */
        $sessions = $sessionRepository->findBy(array(), array('id' => 'desc'));

        $grilles = $grilleRepository->indexToInstance();

        return $this->render('session/index.html.twig', ["sessions" => $sessions, "grilles" => $grilles]);
    }

    #[Route('/creer', name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        SgapRepository         $sgapRepository,
        ConcoursRepository     $concours_repository,
        Request                $request): Response
    {
        $concours = $concours_repository->findAll();
        $sgaps = $sgapRepository->findAll();


        if (empty($sgaps)) {
            $this->addFlash("warning", "Pas de SGAP disponible, veuillez en créer un.");
            return $this->redirectToRoute("sgap_index");
        }

        if (empty($concours)) {
            $this->addFlash("warning", "Pas de concours disponible, veuillez en créer un.");
            return $this->redirectToRoute("concours_index");
        }

        $session = new Session(
            id: 0,
            date: new DateTime("now"),
            numero_ordre: 0,
            observations: "",
            concours: $concours[0],
            sgap: $sgaps[0],
            reponses_candidats: new ArrayCollection()
        );

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form->createView()]
        );
    }

    #[Route('/modifier/{id}', name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        SessionRepository      $sessionRepository,
        Request                $request,
        int                    $id): Response
    {
        $session = $sessionRepository->find($id);

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form->createView()]
        );
    }

    #[Route('/consulter/{id}', name: "consulter")]
    public function consulter(SessionRepository $sessionRepository, int $id): Response
    {
        $session = $sessionRepository->find($id);

        return $this->render('session/session.html.twig', ["session" => $session]);
    }

    #[Route("/csv/{id}", name: "csv")]
    public function csv(
        ReponsesCandidatStorage $reponsesCandidatStorage,
        SessionRepository       $sessionRepository,
        int                     $id
    ): Response
    {
        $session = $sessionRepository->find($id);
        $reponsesCandidatStorage->setFromSession($session);

        return $this->redirectToRoute("csv_reponses");
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(ManagerRegistry         $doctrine,
                              ReponsesCandidatStorage $reponsesCandidatStorage,
                              SessionRepository       $sessionRepository,
                              int                     $id): Response
    {
        $session = $sessionRepository->find($id);

        if ($session != null) {

            $reponsesCandidatStorage->set(array()); // TODO be more specific ?, that is very conservative

            foreach ($session->reponses_candidats->toArray() as $candidat_reponse) {
                $doctrine->getManager()->remove($candidat_reponse);
            }

            $doctrine->getManager()->remove($session);
            $doctrine->getManager()->flush();

            $this->addFlash("success", "La session a bien été supprimée.");
        } else {
            $this->addFlash("danger", "La session n'existe pas ou a déjà été supprimée.");
        }

        return $this->redirectToRoute("session_index");
    }
}