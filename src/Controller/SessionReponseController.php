<?php

namespace App\Controller;

use App\Constants\Sgaps;
use App\Core\Res\Grille\GrilleRepository;
use App\Entity\CandidatReponse;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\CandidatReponseRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session", name: "session_")]
class SessionReponseController extends AbstractController
{

    #[Route('/consulter-liste', name: "consulter_liste")]
    public function sessionsConsulter(
        SessionRepository $session_repository,
        GrilleRepository  $grille_repository
    ): Response
    {
        /** @var array $session */
        $sessions = $session_repository->findAll();
        $grilles = [];

        foreach ($sessions as $session) {
            $grilles[$session->id] = $grille_repository->get($session->grille_id)->getNom();
        }

        return $this->render('sessions/sessions.html.twig', ["sessions" => $sessions, "grilles" => $grilles]);
    }

    #[Route('/creer', name: "creer")]
    public function sessionCreer(
        ManagerRegistry  $doctrine,
        Request          $request,
        Sgaps            $sgaps,
        GrilleRepository $grille_repository): Response
    {
        $session = new Session(
            id: 0,
            date: new DateTime("now"),
            sgap_index: $sgaps->sample(),
            grille_id: $grille_repository->sample(),
            reponses_candidats: new ArrayCollection()
        );

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->persist($session);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'sessions/session_edit.html.twig',
            ["form" => $form]
        );
    }

    #[Route('/modifier/{id}', name: "modifier")]
    public function sessionModifier(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        /** @var Session $session */
        $session = $doctrine->getManager()->find(Session::class, $id);

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'sessions/session_edit.html.twig',
            ["form" => $form]
        );
    }

    #[Route('/consulter/{id}', name: "consulter")]
    public function sessionConsulter(SessionRepository $session_repository, int $id): Response
    {
        $session = $session_repository->find($id);

        return $this->render(
            'sessions/session.html.twig',
            ["session" => $session]
        );
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimerSession(ManagerRegistry           $doctrine,
                                     SessionRepository         $session_repository,
                                     CandidatReponseRepository $candidat_reponse_repository,
                                     int                       $id)
    {

        $session = $session_repository->find($id);

        if ($session != null) {

            foreach ($session->reponses_candidats->toArray() as $candidat_reponse) {
                $doctrine->getManager()->remove($candidat_reponse);
            }

            $doctrine->getManager()->remove($session);
            $doctrine->getManager()->flush();
        }


        return $this->redirectToRoute("session_consulter_liste");
    }

    #[Route("/supprimer-reponse/{session_id}/{id}", name: "supprimer_reponse")]
    public function supprimerCandidatReponse(ManagerRegistry $doctrine, int $session_id, int $id)
    {
        $doctrine->getManager()->remove($doctrine->getManager()->find(CandidatReponse::class, $id));
        $doctrine->getManager()->flush();

        return $this->redirectToRoute("session_consulter", ["id" => $session_id]);
    }

}