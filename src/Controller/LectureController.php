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
        EntityManagerInterface   $entity_manager,
    ): Response
    {
        $reponse = new ReponseCandidat(
            id: 0,
            session: $session_repository->findOneBy([]),
            reponses: array(),
            nom: "",
            prenom: "",
            nom_jeune_fille: "",
            niveau_scolaire: $niveau_scolaire_repository->findOneBy([]),
            date_de_naissance: new DateTime("now"),
            sexe: 1,
            reserve: "",
            autre_1: "",
            autre_2: "",
            code_barre: 0,
            raw: null
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

    #[
        Route("/fichier", name: 'fichier')]
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
                    id: 0,
                    session: $uploadSessionBase->session,
                    reponses: $reponse_array,
                    nom: $reponses_candidat_json["nom"],
                    prenom: $reponses_candidat_json["prenom"],
                    nom_jeune_fille: $reponses_candidat_json["nom_jeune_fille"],
                    niveau_scolaire: $niveau_scolaire_repository->findOneBy([]),
                    date_de_naissance: new DateTime("now"),
                    sexe: $reponses_candidat_json["sexe"],
                    reserve: $reponses_candidat_json["reserve"],
                    autre_1: $reponses_candidat_json["autre_1"],
                    autre_2: $reponses_candidat_json["autre_2"],
                    code_barre: $reponses_candidat_json["code_barre"],
                    raw: $reponses_candidat_json
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
        // Spécifier le port. A priori, il faut un ":" à la fin. On trouve le numéro du port grâce à la ligne de commande "mode" dans le terminal
        $fd = dio_open("COM3:", O_RDWR);

        // Normalement, c'est les bons paramètres
        dio_tcsetattr($fd, array(
            'baud' => 19200,
            'bits' => 8,
            'stop' => 1,
            'parity' => 0
        ));

        // https://www.php.net/manual/en/function.dio-write.php
        dio_write($fd, data: "v\n", len: 1); // ecrit 1 bit de la chaine de caractère data

        // https://www.php.net/manual/en/function.dio-read.php
        $x = dio_read($fd, 1); // Lit 1 bit

        dump($x); // dump est pas mal pour le debug, c'est mis en forme par symfony profiler (quand c'est pas juste un nombre)

        dio_close($fd);
        return $this->render("lecture/from_scanner.html.twig", ["form" => null]);
    }

}