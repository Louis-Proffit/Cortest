<?php

namespace App\Controller;

use App\Entity\ReponseCandidat;
use App\Form\Data\ParametresLectureJSON;
use App\Form\ParametresLectureFichierType;
use App\Form\ReponseCandidatType;
use App\Repository\NiveauScolaireRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/lecture", name: "lecture_")]
class LectureController extends AbstractController
{
    #[Route("/index", name: "index")]
    public function lectureHome(): Response
    {
        return $this->render("lecture/index.html.twig");
    }

    #[Route("/form", name: "form")]
    public function form(
        SessionRepository        $session_repository,
        NiveauScolaireRepository $niveau_scolaire_repository,
        Request                  $request,
        EntityManagerInterface   $entity_manager
    ): Response
    {

        $session = $session_repository->findOneBy([]);

        if ($session == null) {
            $this->addFlash("warning", "Pas de séance, en créer une");
            return $this->redirectToRoute("session_creer");
        }

        $niveau_scolaire = $niveau_scolaire_repository->findOneBy([]);
        if ($niveau_scolaire == null) {
            $this->addFlash("warning", "Pas de niveau scolaire, en créer un");
            return $this->redirectToRoute("niveau_scolaire_creer");
        }

        $reponse = new ReponseCandidat(
            0,
            $session,
            array(),
            "",
            "",
            "",
            $niveau_scolaire,
            new DateTime("now"),
            1,
            "",
            "",
            "",
            0,
            null
        );


        $form = $this->createForm(ReponseCandidatType::class, $reponse);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($reponse);
            $entity_manager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $reponse->session->id]);
        }

        return $this->render("lecture/from_form.html.twig", ["form" => $form]);
    }

    #[Route("/fichier", name: 'fichier')]
    public function fichier(ManagerRegistry          $doctrine,
                            NiveauScolaireRepository $niveau_scolaire_repository,
                            Request                  $request,
                            LoggerInterface          $logger): Response
    {
        $manager = $doctrine->getManager();

        $uploadSessionBase = new ParametresLectureJSON();
        $form = $this->createForm(ParametresLectureFichierType::class, $uploadSessionBase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->debug("Fichier de correction reçu : " . $uploadSessionBase->contents);

            /** @var array $decoded */
            $decoded = json_decode($uploadSessionBase->contents, associative: true);

            foreach ($decoded as $reponses_candidat_json) {

                /** @var string $reponse_string */
                $reponse_string = $reponses_candidat_json["reponses"];

                $reponse_array = str_split($reponse_string);

                dump($reponse_array);

                $reponse_candidat = new ReponseCandidat(
                    0,
                    $uploadSessionBase->session,
                    $reponse_array,
                    $reponses_candidat_json["nom"],
                    $reponses_candidat_json["prenom"],
                    $reponses_candidat_json["nom_jeune_fille"],
                    $niveau_scolaire_repository->findOneBy([]),
                    new DateTime("now"),
                    $reponses_candidat_json["sexe"],
                    $reponses_candidat_json["reserve"],
                    $reponses_candidat_json["autre_1"],
                    $reponses_candidat_json["autre_2"],
                    $reponses_candidat_json["code_barre"],
                    $reponses_candidat_json
                );
                $manager->persist($reponse_candidat);
            }

            $manager->flush();

            return $this->redirectToRoute('session_consulter', ["id" => $uploadSessionBase->session->id]);
        }

        return $this->render('lecture/from_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/scanner", name: "scanner")]
    public function scanner(): Response
    {
        //si pas de session spécifiée, la demander !
        //si la session est renseignée : ($session disponible)
        //ancienne vue
        return $this->render("lecture/from_scanner.html.twig", ["form" => null]);
        //nouvelle vue
        //return $this->render("lecture/lecteur_optique.html.twig", ["form" => null]);
    }

}
