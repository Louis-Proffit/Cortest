<?php

namespace App\Controller;

use App\Entity\CandidatReponse;
use App\Entity\Session;
use App\Form\SessionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/session", name: "session_")]
class SessionReponseController extends AbstractController
{

    #[Route('/form', name: "consulter_liste")]
    public function sessionsConsulter(ManagerRegistry $doctrine): Response
    {
        /** @var array $session */
        $sessions = $doctrine->getRepository(Session::class)->findAll();

        return $this->render('sessions/sessions.html.twig', ["sessions" => $sessions]);
    }

    #[Route('/edit-{id}', name: "modifier")]
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

    #[Route('/{id}', name: "consulter")]
    public function sessionConsulter(ManagerRegistry $doctrine, int $id): Response
    {
        /** @var Session $session */
        $session = $doctrine->getManager()->find(Session::class, $id);

        return $this->render(
            'sessions/session.html.twig',
            ["session" => $session]
        );
    }

    #[Route("/supprimer-session/{id}", name: "supprimer_session")]
    public function supprimerSession(ManagerRegistry $doctrine, int $id)
    {
        $doctrine->getManager()->remove($doctrine->getManager()->find(Session::class, $id));
        $doctrine->getManager()->flush();

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