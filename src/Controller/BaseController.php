<?php

namespace App\Controller;

use App\Core\Entities\ProfilOuScore;
use App\Core\Entities\ScoreComputer;
use App\Entity\CandidatReponse;
use App\Entity\CandidatScore;
use App\Entity\DefinitionEtalonnageComputer;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use App\Form\SessionProfilForm;
use App\Form\SessionProfilFormType;
use App\Form\SessionScoreForm;
use App\Form\UploadSessionBase;
use App\Form\UploadSessionBaseType;
use App\Repository\RuntimeResourcesRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/home', name: "home")]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'hello'
        ]);
    }

    #[Route("/calcul/base", name: 'calcul_base')]
    public function calculerBase(ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): Response
    {
        $manager = $doctrine->getManager();
        $sessions = $manager->getRepository(Session::class)->findAll();

        $uploadSessionBase = new UploadSessionBase();
        $form = $this->createForm(UploadSessionBaseType::class, $uploadSessionBase, ["sessions" => $sessions]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $logger->debug($uploadSessionBase->contents);

            /** @var Session $session */
            $session = $manager->find(Session::class, $uploadSessionBase->session_id);

            /** @var array $decoded */
            $decoded = json_decode($uploadSessionBase->contents, associative: true);

            foreach ($decoded as $candidat_reponse_json) {
                var_dump($candidat_reponse_json);
                $manager->persist(
                    new CandidatReponse(id: 0, session: $session, reponses: $candidat_reponse_json)
                );
            }

            $manager->flush();

            return $this->redirectToRoute('consulter_sessions', ["id" => $uploadSessionBase->session_id]);
        }

        return $this->render('scanner/scanner_csv_base.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sessions', name: "consulter_sessions")]
    public function sessionsConsulter(ManagerRegistry $doctrine): Response
    {
        /** @var array $session */
        $sessions = $doctrine->getRepository(Session::class)->findAll();

        return $this->render('session/sessions.html.twig', ["sessions" => $sessions]);
    }

    #[Route('/session/{id}', name: "consulter_session")]
    public function sessionConsulter(ManagerRegistry $doctrine, int $id): Response
    {
        /** @var Session $session */
        $session = $doctrine->getManager()->find(Session::class, $id);

        return $this->render(
            'session/session.html.twig',
            ["session" => $session]
        );
    }

    #[Route('/score/pdf/{id}', name: "consulter_score_pdf")]
    public function scorePdfConsulter(ManagerRegistry $doctrine, int $id): Response
    {
        /** @var CandidatScore $score */
        //$score = $doctrine->getManager()->find(CandidatScore::class, $id);

        // TODO
        return $this->file("test.pdf", "score_candidat.pdf", ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route("/candiat_score/{id}", name: "consulter_candidat_score")]
    public function consulterScore(ManagerRegistry $doctrine, int $id): Response
    {
        $candidat_score = $doctrine->getManager()->find(CandidatScore::class, $id);

        return $this->render("session/candidat_score.html.twig", ["candidat_score" => $candidat_score]);
    }

    // TODO renomer les routes pour être cohérent avec les routes du ui
    /*#[Route('/score/{id}', name: 'calculer_score')]
    public function scoreCalculator(Request $request, int $id): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'user'
        ]);
    }*/

    #[Route('/reponse/{id}', name: 'calculer_reponse')]
    public function reponseCalculator(Request $request, int $id): Response
    {
        return $this->render('home/home.html.twig', [
            'last_username' => 'user'
        ]);
    }
}
