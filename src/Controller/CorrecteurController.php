<?php

namespace App\Controller;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Files\FileNameManager;
use App\Core\Files\FileUtils;
use App\Core\IO\Correcteur\ExportCorrecteurXML;
use App\Core\IO\Correcteur\ImportCorrecteurXML;
use App\Core\IO\Correcteur\ImportCorrecteurXMLErrorHandlerAddFlash;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Form\CorrecteurCreerType;
use App\Form\CorrecteurType;
use App\Form\Data\CorrecteurCreer;
use App\Form\ImportCorrecteurType;
use App\Repository\ConcoursRepository;
use App\Repository\CorrecteurRepository;
use App\Repository\GrilleRepository;
use App\Repository\StructureRepository;
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
        CorrecteurRepository $correcteurRepository,
        GrilleRepository     $grilleRepository
    ): Response
    {
        $correcteurs = $correcteurRepository->findAll();

        $grilles = $grilleRepository->indexToInstance();

        return $this->render('correcteur/index.html.twig',
            ["correcteurs" => $correcteurs, "grilles" => $grilles]);
    }

    #[Route("/consulter/{id}", name: 'consulter')]
    public function consulter(
        GrilleRepository $grilleRepository,
        Correcteur       $correcteur
    ): Response
    {
        $grille = $grilleRepository->getFromIndex($correcteur->tests->index_grille);

        return $this->render("correcteur/correcteur.html.twig",
            ["correcteur" => $correcteur, "grille" => $grille]);
    }

    #[Route("/importer", name: "importer")]
    public function importer(
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

                if(count($errors) == 0) {
                    $entityManager->persist($correcteur);
                    $entityManager->flush();

                    $this->addFlash("success", "Correcteur importé");

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
        EntityManagerInterface $entityManager,
        ConcoursRepository     $concoursRepository,
        StructureRepository    $profilRepository,
        Request                $request
    ): Response
    {
        $profils = $profilRepository->findAll();

        if (empty($profils)) {
            $this->addFlash("warning", "Pas de profils disponibles, veuillez en créer un.");
            return $this->redirectToRoute("profil_index");
        }

        $allConcours = $concoursRepository->findAll();

        if (empty($allConcours)) {
            $this->addFlash("warning", "Pas de concours disponible, veuillez en créer un.");
            return $this->redirectToRoute("concours_index");
        }

        $correcteurCreer = new CorrecteurCreer(
            profil: $profils[0],
            concours: $allConcours[0],
            nom: ""
        );

        $form = $this->createForm(CorrecteurCreerType::class, $correcteurCreer);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $profil = $correcteurCreer->profil;
            $concours = $correcteurCreer->concours;

            $correcteur = new Correcteur(
                id: 0,
                concours: $concours,
                profil: $profil,
                nom: $correcteurCreer->nom,
                echelles: new ArrayCollection()
            );

            foreach ($profil->echelles as $echelle) {

                $echelleCorrecteur = new EchelleCorrecteur(
                    id: 0, expression: "0", echelle: $echelle, correcteur: $correcteur
                );

                $echelleCorrecteur->echelle = $echelle;
                $echelleCorrecteur->id = 0;
                $echelleCorrecteur->expression = "0";

                $correcteur->echelles->add($echelleCorrecteur);
            }

            $entityManager->persist($correcteur);
            $entityManager->flush();

            return $this->redirectToRoute("correcteur_modifier", ["id" => $correcteur->id]);
        }

        return $this->render("correcteur/form.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ManagerRegistry           $doctrine,
        CortestExpressionLanguage $cortestExpressionLanguage,
        Request                   $request,
        Correcteur                $correcteur
    ): Response
    {
        $form = $this->createForm(CorrecteurType::class, $correcteur);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);

        }

        $fonctions = $cortestExpressionLanguage->getCortestFunctions();

        return $this->render("correcteur/modifier.html.twig",
            ["form" => $form->createView(), "fonctions" => $fonctions]);
    }

    #[Route("/exporter/{id}", name: "exporter")]
    public function exporter(
        ExportCorrecteurXML $exportCorrecteurXML,
        FileNameManager     $fileNameManager,
        Correcteur          $correcteur
    ): Response
    {
        $xml = $exportCorrecteurXML->export($correcteur);

        if (!$xml) {

            $this->addFlash("danger", "Erreur lors de l'export");
            return $this->redirectToRoute("correcteur_consulter", ["id" => $correcteur->id]);

        } else {

            $response = new Response($xml);

            $fileName = $fileNameManager->correcteurXmlFileName($correcteur);

            FileUtils::setFileResponseFileName($response, $fileName);

            return $response;
        }
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Correcteur             $correcteur
    ): Response
    {
        $entityManager->remove($correcteur);

        $this->addFlash("success", "Correcteur supprimé");
        $logger->info("Suppression du correcteur " . $correcteur->id);

        $entityManager->flush();

        return $this->redirectToRoute("correcteur_index");
    }

}