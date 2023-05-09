<?php

namespace App\Controller;

use App\Core\Files\Csv\CsvManager;
use App\Core\Files\Csv\CsvReponseManager;
use App\Core\Files\Csv\Reponses\ReponsesCandidatExport;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
        $sessions = $session_repository->findBy(array(), array('id' => 'desc'));

        $grilles = $grille_repository->indexToInstance();

        return $this->render('session/index.html.twig', ["sessions" => $sessions, "grilles" => $grilles]);
    }

    #[Route('/creer', name: "creer")]
    public function creer(
        EntityManagerInterface $entity_manager,
        SgapRepository         $sgap_repository,
        ConcoursRepository     $concours_repository,
        Request                $request): Response
    {
        $concours = $concours_repository->findAll();
        $sgaps = $sgap_repository->findAll();


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

            $entity_manager->persist($session);
            $entity_manager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $session->id]);
        }

        return $this->render(
            'session/modifier.html.twig',
            ["form" => $form->createView()]
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
            ["form" => $form->createView()]
        );
    }

    #[Route('/consulter/{id}', name: "consulter")]
    public function consulter(SessionRepository $session_repository, int $id): Response
    {
        $session = $session_repository->find($id);

        return $this->render('session/session.html.twig', ["session" => $session]);
    }

    #[Route("/csv/{id}", name: "csv")]
    public function csv(
        ReponsesCandidatExport $reponsesCandidatExport,
        CsvManager             $csvManager,
        SessionRepository      $session_repository,
        int                    $id
    ): BinaryFileResponse
    {
        $session = $session_repository->find($id);

        $fileName = "session_" . $session->date->format("d-m-Y") . "_" . $session->concours->nom . ".csv";

        $raw = $reponsesCandidatExport->export($session->reponses_candidats->toArray());

        return $csvManager->export($raw, $fileName);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(ManagerRegistry   $doctrine,
                              SessionRepository $session_repository,
                              int               $id): Response
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