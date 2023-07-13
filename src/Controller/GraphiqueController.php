<?php

namespace App\Controller;

use App\Core\Correcteur\CorrecteurManager;
use App\Core\Etalonnage\EtalonnageManager;
use App\Core\Files\FileUtils;
use App\Core\Files\Pdf\Compiler\LatexCompilationFailedException;
use App\Core\Files\Pdf\PdfManager;
use App\Core\Files\Pdf\Renderer;
use App\Entity\Graphique;
use App\Form\CorrecteurEtEtalonnageChoiceType;
use App\Form\Data\CorrecteurEtEtalonnageChoice;
use App\Form\GraphiqueType;
use App\Repository\GraphiqueRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

        return $this->render('graphique/index.html.twig',
            ["graphiques" => $graphiques]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        ProfilRepository       $profilRepository,
        Request                $request
    ): Response
    {
        $profils = $profilRepository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créez un");
            return $this->redirectToRoute("profil_index");
        }

        $graphique = new Graphique(
            id: 0,
            profil: $profils[0],
            nom: "", content: ""
        );

        $form = $this->createForm(GraphiqueType::class, $graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($graphique);
            $entityManager->flush();


            return $this->redirectToRoute("graphique_index");
        }

        return $this->render("graphique/creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Graphique              $graphique,
    ): Response
    {
        $form = $this->createForm(GraphiqueType::class, $graphique);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->persist($graphique);
            $entityManager->flush();

            return $this->redirectToRoute("graphique_index");
        }

        return $this->render("graphique/modifier.html.twig", ["form" => $form->createView()]);
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
    ): BinaryFileResponse|Response
    {
        $correcteurEtalonnage = new CorrecteurEtEtalonnageChoice();

        $form = $this->createForm(CorrecteurEtEtalonnageChoiceType::class, $correcteurEtalonnage, [CorrecteurEtEtalonnageChoiceType::OPTION_PROFIL => $graphique->profil]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $correcteurEtalonnage->both->correcteur;
            $etalonnage = $correcteurEtalonnage->both->etalonnage;

            $reponsesCandidat = $renderer->dummyReponse($correcteur->concours);
            $reponsesCandidats = [$reponsesCandidat];

            $scores = $correcteurManager->corriger(
                $correcteur,
                $reponsesCandidats
            );

            $profils = $etalonnageManager->etalonner(
                $etalonnage,
                scores: $scores
            );

            $response = $pdfManager->createPdfFile(
                graphique: $graphique,
                reponseCandidat: $reponsesCandidat,
                correcteur: $correcteur, etalonnage: $etalonnage, score: $scores[0],
                profil: $profils[0]
            );

            if (!$response) {
                $this->addFlash("danger", "Echec du test, vérifiez le fichier .tex");
                return $this->redirectToRoute("graphique_index");
            }

            return $response;
        }

        return $this->render("graphique/tester_form.twig", ["form" => $form->createView()]);
    }

    #[Route("/telecharger/{id}", name: "telecharger")]
    public function telecharger(Graphique $graphique): Response
    {
        $response = new Response($graphique->content);
        FileUtils::setFileResponseFileName($response, $graphique->nom . ".tex.twig");
        return $response;
    }

    #[Route("/verifier-variables", name: "verifier_variables")]
    public function verifierVariables(
        Request  $request,
        Renderer $renderer,
    ): Response
    {
        $correcteurEtalonnage = new CorrecteurEtEtalonnageChoice();
        $form = $this->createForm(CorrecteurEtEtalonnageChoiceType::class, $correcteurEtalonnage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $correcteur = $correcteurEtalonnage->both->correcteur;
            $etalonnage = $correcteurEtalonnage->both->etalonnage;

            $keys = $renderer->optionKeys(
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