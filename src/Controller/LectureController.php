<?php

namespace App\Controller;

use App\Entity\CandidatReponse;
use App\Entity\Session;
use App\Form\Data\ParametresLectureFichier;
use App\Form\ParametresLectureFichierType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/lecture", name: "lecture_")]
class LectureController extends AbstractController
{
    #[Route("/", name: "home")]
    public function lectureHome(): Response
    {
        return $this->render("lecture/index.html.twig");
    }

    #[Route("/fichier", name: 'fichier')]
    public function fichier(ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): Response
    {
        $manager = $doctrine->getManager();

        $uploadSessionBase = new ParametresLectureFichier();
        $form = $this->createForm(ParametresLectureFichierType::class, $uploadSessionBase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->debug("Fichier de correction reçu : " . $uploadSessionBase->contents);

            /** @var Session $session */
            $session = $manager->find(Session::class, $uploadSessionBase->session_id);

            /** @var array $decoded */
            $decoded = json_decode($uploadSessionBase->contents, associative: true);

            foreach ($decoded as $candidat_reponse_json) {

                $manager->persist(
                    new CandidatReponse(id: 0, session: $session, reponses: $candidat_reponse_json)
                );
            }

            $manager->flush();

            return $this->redirectToRoute('session_consulter', ["id" => $uploadSessionBase->session_id]);
        }

        return $this->render('lecture/from_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/scanner", name: "scanner")]
    public function scanner(): Response
    {
        return $this->render("lecture/from_scanner.html.twig", ["form" => null]);
    }

}