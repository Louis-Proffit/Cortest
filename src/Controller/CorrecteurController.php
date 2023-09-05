<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Core\IO\Correcteur\ExportCorrecteurXML;
use App\Core\IO\Correcteur\ImportCorrecteurXML;
use App\Core\IO\Correcteur\ImportCorrecteurXMLErrorHandlerAddFlash;
use App\Core\IO\FileNameManager;
use App\Core\IO\FileUtils;
use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Entity\Correcteur;
use App\Entity\CortestLogEntry;
use App\Entity\EchelleCorrecteur;
use App\Form\CorrecteurCreerType;
use App\Form\CorrecteurType;
use App\Form\ImportCorrecteurType;
use App\Repository\CorrecteurRepository;
use App\Repository\StructureRepository;
use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/correcteur", name: "correcteur_")]
class CorrecteurController extends AbstractController
{
    #[Route("/index", name: 'index')]
    public function index(
        CorrecteurRepository $correcteurRepository
    ): Response
    {
        $correcteurs = $correcteurRepository->findAll();

        return $this->render('correcteur/index.html.twig',
            ["correcteurs" => $correcteurs]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        Correcteur $correcteur
    ): Response
    {
        return $this->render("correcteur/correcteur.html.twig",
            ["correcteur" => $correcteur]);
    }

    #[Route("/importer", name: "importer")]
    public function importer(
        ActiviteLogger         $activiteLogger,
        ValidatorInterface     $validator,
        EntityManagerInterface $entityManager,
        Session                $session,
        ImportCorrecteurXML    $importCorrecteurXML,
        Request                $request
    ): Response
    {
        $form = $this->createForm(ImportCorrecteurType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get(ImportCorrecteurType::FILE_KEY)->getData();

            $correcteur = $importCorrecteurXML->load(new ImportCorrecteurXMLErrorHandlerAddFlash($session), $file->getContent());

            if ($correcteur) {

                $errors = $validator->validate($correcteur);

                if (count($errors) == 0) {
                    $entityManager->persist($correcteur);
                    $activiteLogger->persistAction(
                        action: CortestLogEntry::ACTION_CREER,
                        object: $correcteur,
                        message: "Import d'une correction par un fichier XML"
                    );
                    $entityManager->flush();

                    $this->addFlash("success", "Correction importée");

                    return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);
                } else {
                    /** @var ConstraintViolationInterface $error */
                    foreach ($errors as $error) {
                        $this->addFlash("danger", $error->getMessage());
                    }
                }
            }
        }

        return $this->render("correcteur/form_importer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        ActiviteLogger         $activiteLogger,
        EntityManagerInterface $entityManager,
        TestRepository         $testRepository,
        StructureRepository    $structureRepository,
        Request                $request
    ): Response
    {
        $structures = $structureRepository->findAll();

        if (empty($structures)) {
            $this->addFlash("warning", "Pas de structure disponible, veuillez en créer une.");
            return $this->redirectToRoute("structure_index");
        }

        $tests = $testRepository->findAll();

        if (empty($tests)) {
            $this->addFlash("warning", "Pas de test disponible, veuillez en créer au moins un.");
            return $this->redirectToRoute("test_index");
        }

        $correcteur = new Correcteur(
            id: 0,
            tests: new ArrayCollection(),
            structure: $structures[0],
            nom: "",
            echelles: new ArrayCollection()
        );

        $form = $this->createForm(CorrecteurCreerType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            foreach ($correcteur->structure->echelles as $echelle) {

                $echelleCorrecteur = new EchelleCorrecteur(
                    id: 0, expression: "0", echelle: $echelle, correcteur: $correcteur
                );

                $echelleCorrecteur->echelle = $echelle;
                $echelleCorrecteur->id = 0;
                $echelleCorrecteur->expression = "0";

                $correcteur->echelles->add($echelleCorrecteur);
            }

            $entityManager->persist($correcteur);
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $correcteur,
                message: "Création d'une correction par formulaire"
            );
            $entityManager->flush();

            return $this->redirectToRoute("correcteur_modifier", ["id" => $correcteur->id]);
        }

        return $this->render("correcteur/form_creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ActiviteLogger            $activiteLogger,
        EntityManagerInterface    $entityManager,
        CortestExpressionLanguage $cortestExpressionLanguage,
        Request                   $request,
        Correcteur                $correcteur
    ): Response
    {
        $form = $this->createForm(CorrecteurType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $correcteur,
                message: "Modification d'une correction par formulaire"
            );
            $entityManager->flush();

            return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);

        }

        $fonctions = $cortestExpressionLanguage->getCortestFunctions();

        return $this->render("correcteur/form_modifier.html.twig",
            ["form" => $form->createView(), "fonctions" => $fonctions]);
    }

    #[Route("/exporter/{id}", name: "exporter")]
    public function exporter(
        ActiviteLogger      $activiteLogger,
        ExportCorrecteurXML $exportCorrecteurXML,
        FileNameManager     $fileNameManager,
        Correcteur          $correcteur
    ): Response
    {
        $xml = $exportCorrecteurXML->export($correcteur);

        if (!$xml) {

            $this->addFlash("danger", "Erreur lors de l'export de la correction");
            return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);

        } else {

            $response = new Response($xml);

            $fileName = $fileNameManager->correcteurXmlFileName($correcteur);

            FileUtils::setFileResponseFileName($response, $fileName);

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_EXPORTER,
                object: $correcteur,
                message: "Export d'une correction vers un fichier xml",
                data: ["fichier" => $fileName]
            );

            $activiteLogger->flush();

            return $response;
        }
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger         $activiteLogger,
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Correcteur             $correcteur
    ): Response
    {
        $this->addFlash("success", "Correcteur supprimé");
        $logger->info("Suppression du correcteur.", ["correcteur" => $correcteur]);

        $activiteLogger->persistAction(
            action: CortestLogEntry::ACTION_SUPPRIMER,
            object: $correcteur,
            message: "Suppression d'une correction"
        );
        $entityManager->remove($correcteur);
        $entityManager->flush();

        return $this->redirectToRoute("correcteur_index");
    }

}