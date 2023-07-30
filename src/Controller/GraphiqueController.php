<?php

namespace App\Controller;

use App\Core\Exception\UploadFailException;
use App\Core\IO\GraphiqueFileRepository;
use App\Core\IO\Pdf\Compiler\LatexCompilationFailedException;
use App\Core\IO\Pdf\PdfManager;
use App\Core\IO\Pdf\Renderer;
use App\Core\ScoreBrut\CorrecteurManager;
use App\Core\ScoreEtalonne\EtalonnageManager;
use App\Entity\Graphique;
use App\Form\Data\TestCorrecteurEtalonnageChoice;
use App\Form\GraphiqueType;
use App\Form\TestCorrecteurEtalonnageChoiceType;
use App\Repository\GraphiqueRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/graphique", name: "graphique_")]
class GraphiqueController extends AbstractController
{

    #[Route("/index", name: 'index')]
    public function index(
        GraphiqueRepository $graphiqueRepository
    ): Response
    {
        $graphiques = $graphiqueRepository->findAll();

        return $this->render('graphique/index.html.twig', ["graphiques" => $graphiques]);
    }

    /**
     * @throws UploadFailException
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        GraphiqueFileRepository $graphiqueFileManager,
        EntityManagerInterface  $entityManager,
        StructureRepository     $structureRepository,
        Request                 $request
    ): Response
    {
        $structures = $structureRepository->findAll();

        if (empty($structures)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créez un");
            return $this->redirectToRoute("structure_index");
        }

        $graphique = new Graphique(
            id: 0,
            structure: $structures[0],
            nom: "",
            file_nom: ""
        );

        $form = $this->createForm(GraphiqueType::class, $graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get(GraphiqueType::FILE_KEY)->getData();

            $entityManager->persist($graphique);
            $entityManager->flush();

            $graphiqueFileManager->upload($file, $graphique);

            return $this->redirectToRoute("graphique_index");
        }

        return $this->render("graphique/form_creer.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @throws UploadFailException
     */
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        GraphiqueFileRepository $graphiqueFileManager,
        EntityManagerInterface  $entityManager,
        Request                 $request,
        Graphique               $graphique,
    ): Response
    {
        $form = $this->createForm(GraphiqueType::class, $graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get(GraphiqueType::FILE_KEY)->getData();

            $entityManager->flush();

            $graphiqueFileManager->upload($file, $graphique);

            return $this->redirectToRoute("graphique_index");
        }

        return $this->render("graphique/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @throws LatexCompilationFailedException
     */
    #[Route("/tester/{id}", name: "tester")]
    public function tester(
        Renderer          $renderer,
        CorrecteurManager $correcteurManager,
        EtalonnageManager $etalonnageManager,
        Request           $request,
        PdfManager        $pdfManager,
        Graphique         $graphique
    ): Response
    {
        $correcteurEtalonnage = new TestCorrecteurEtalonnageChoice();

        $form = $this->createForm(TestCorrecteurEtalonnageChoiceType::class, $correcteurEtalonnage, [TestCorrecteurEtalonnageChoiceType::OPTION_STRUCTURE => $graphique->structure]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $correcteurEtalonnage->value->correcteur;
            $etalonnage = $correcteurEtalonnage->value->etalonnage;

            $reponsesCandidat = $renderer->dummyReponse($correcteur->tests[0]);
            $reponsesCandidats = [$reponsesCandidat];

            $scores = $correcteurManager->corriger(
                $correcteur,
                $reponsesCandidats
            );

            $profils = $etalonnageManager->etalonner(
                $etalonnage,
                scoresBruts: $scores
            );

            $response = $pdfManager->createPdfFile(
                graphique: $graphique,
                reponseCandidat: $reponsesCandidat,
                correcteur: $correcteur,
                etalonnage: $etalonnage,
                scoreBrut: $scores[0],
                scoreEtalonne: $profils[0]
            );

            if (!$response) {
                $this->addFlash("danger", "Echec du test, vérifiez le fichier .tex");
                return $this->redirectToRoute("graphique_index");
            }

            return $response;
        }

        return $this->render("graphique/form_tester.twig", ["form" => $form->createView()]);
    }

    #[Route("/download/{id}", name: "download")]
    public function telecharger(
        Graphique            $graphique,
        GraphiqueFileRepository $graphiqueFileManager
    ): Response
    {
        $filePath = $graphiqueFileManager->entityFilePathOrNull($graphique);

        if ($filePath == null) {
            $this->addFlash("danger", "Le fichier n'existe pas.");
            return $this->redirectToRoute("graphique_index");
        } else {
            return $this->file($filePath, $graphique->file_nom);
        }
    }

    #[Route("/verifier-variables", name: "verifier_variables")]
    public function verifierVariables(
        Request  $request,
        Renderer $renderer,
    ): Response
    {
        $testCorrecteurEtalonnage = new TestCorrecteurEtalonnageChoice();
        $form = $this->createForm(TestCorrecteurEtalonnageChoiceType::class, $testCorrecteurEtalonnage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $test = $testCorrecteurEtalonnage->value->test;
            $correcteur = $testCorrecteurEtalonnage->value->correcteur;
            $etalonnage = $testCorrecteurEtalonnage->value->etalonnage;

            $keys = $renderer->optionKeys(
                test: $test,
                correcteur: $correcteur,
                etalonnage: $etalonnage
            );

            return $this->render("graphique/verifier_variables.html.twig", ["correcteur" => $correcteur, "etalonnage" => $etalonnage, "keys" => $keys]);
        }

        return $this->render("graphique/verifier_variables_form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Graphique              $graphique
    ): Response
    {
        $logger->info("Suppression du graphique " . $graphique->id);

        $entityManager->remove($graphique);
        $entityManager->flush();

        $this->addFlash("success", "Suppression du graphique enregistrée");

        return $this->redirectToRoute("graphique_index");
    }

}