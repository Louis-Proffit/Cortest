<?php

namespace App\Controller;

use App\Core\Files\CsvManager;
use App\Core\IO\ReponseCandidat\ImportReponsesCandidat;
use App\Core\IO\ReponseCandidat\ImportReponsesCandidatException;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Form\Data\ParametresLectureCsv;
use App\Form\Data\ParametresLectureJSON;
use App\Form\Data\ParametresLectureOptique;
use App\Form\ParametresLectureFichierCsvType;
use App\Form\ParametresLectureFichierType;
use App\Form\ParametresLectureOptiqueType;
use App\Form\ReponseCandidatType;
use App\Repository\NiveauScolaireRepository;
use App\Repository\ReponseCandidatRepository;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $this->addFlash("warning", "Pas de séance, veuilez en créer une");
            return $this->redirectToRoute("session_creer");
        }

        $niveau_scolaire = $niveau_scolaire_repository->findOneBy([]);
        if ($niveau_scolaire == null) {
            $this->addFlash("warning", "Pas de niveau scolaire, veuillez en créer un");
            return $this->redirectToRoute("niveau_scolaire_creer");
        }

        $reponse = new ReponseCandidat(
            id: 0,
            session: $session,
            reponses: array(),
            nom: "",
            prenom: "",
            nom_jeune_fille: "",
            niveau_scolaire: $niveau_scolaire,
            date_de_naissance: new DateTime("now"),
            sexe: 1,
            reserve: "",
            autre_1: "",
            autre_2: "",
            code_barre: 0,
            eirs: ReponseCandidat::TYPE_E,
            raw: null
        );


        $form = $this->createForm(ReponseCandidatType::class, $reponse);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entity_manager->persist($reponse);
            $entity_manager->flush();

            return $this->redirectToRoute("session_consulter", ["id" => $reponse->session->id]);
        }

        return $this->render("lecture/from_form.html.twig", ["form" => $form->createView()]);
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
                    eirs: $reponses_candidat_json["EIRS"],
                    raw: $reponses_candidat_json
                );
                $manager->persist($reponse_candidat);
            }
            $manager->flush();

            $this->addFlash('success', 'Le fichier a bien été introduit dans la base de données');
            return $this->redirectToRoute('session_consulter', ["id" => $uploadSessionBase->session->id]);
        }

        return $this->render('lecture/from_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/fichier-csv", name: 'fichier_csv')]
    public function importCsv(ManagerRegistry        $doctrine,
                              Request                $request,
                              CsvManager             $csvManager,
                              ImportReponsesCandidat $reponsesCandidatImport
    ): Response
    {
        $manager = $doctrine->getManager();

        $uploadSessionBase = new ParametresLectureCsv();
        $form = $this->createForm(ParametresLectureFichierCsvType::class, $uploadSessionBase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rawReponsesCandidats = $csvManager->import($uploadSessionBase->contents->getPathname());

            try {
                $reponsesCandidats = $reponsesCandidatImport->import($uploadSessionBase->session, $rawReponsesCandidats);

                foreach ($reponsesCandidats as $reponseCandidat) {
                    $manager->persist($reponseCandidat);
                }

                $manager->flush();
                $this->addFlash('success', 'Le fichier CSV a bien été introduit dans la base de données');

                return $this->redirectToRoute('home');
            } catch (ImportReponsesCandidatException $e) {
                $this->addFlash("danger", $e->getMessage());
            }
        }

        return $this->render('lecture/from_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/scanner", name: "scanner")]
    public function scanner(Request $request): Response
    {
        $uploadSessionBase = new ParametresLectureOptique();
        $form = $this->createForm(ParametresLectureOptiqueType::class, $uploadSessionBase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $session = $uploadSessionBase->session;
            return $this->redirectToRoute("lecture_optique", ["id" => $session->id]);
        }

        return $this->render('lecture/from_scanner_parameters.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/optique/{id}", name: "optique")]
    public function scannerid(NiveauScolaireRepository $niveauScolaireRepository,
                              Session                  $session): Response
    {
        return $this->render("lecture/from_scanner.html.twig",
            ["form" => null,
                "session" => $session,
                "niveaux" => $niveauScolaireRepository->findAll(),
            ]
        );
    }


    #[Route("/scanner/save", name: 'saveFromScanner')]
    public function saveFromScanner(Request                   $request,
                                    EntityManagerInterface    $entityManager,
                                    SessionRepository         $sessionRepository,
                                    NiveauScolaireRepository  $niveauScolaireRepository,
                                    ReponseCandidatRepository $reponseCandidatRepository
    ): Response
    {

        $data = json_decode($request->request->get("data"), true);

        foreach ($data as $i => $ligne) {
            if (count($reponseCandidatRepository->findBy(["nom" => $ligne['nom'], "prenom" => $ligne['prenom']])) == 0) {
                $rep = new ReponseCandidat(
                    id: 0,
                    session: $sessionRepository->find($request->request->get('session')),
                    reponses: $ligne['qcm'],
                    nom: $ligne['nom'],
                    prenom: $ligne['prenom'],
                    nom_jeune_fille: $ligne['nom_jeune_fille'],
                    niveau_scolaire: $niveauScolaireRepository->find($ligne['niveau_scolaire']),
                    date_de_naissance: new DateTime($ligne['date_naissance']),
                    sexe: $ligne['sexe'],
                    reserve: $ligne['reserve'],
                    autre_1: $ligne['option_1'],
                    autre_2: $ligne['option_2'],
                    code_barre: "" . $i,
                    eirs: $ligne['concours'],
                    raw: null
                );
                $entityManager->persist($rep);
                $entityManager->flush();
            }

        }

        return new JsonResponse(['session' => $request->request->get('session'), 'data' => $data]);
    }
}
