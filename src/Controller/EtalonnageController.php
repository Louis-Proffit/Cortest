<?php

namespace App\Controller;

use App\Entity\DefinitionProfilComputer;
use App\Form\EtalonnageType;
use App\Repository\DefinitionProfilComputerRepository;
use App\Repository\DefinitionScoreRepository;
use App\Repository\RuntimeResourcesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/etalonnage", name: "etalonnage_")]
class EtalonnageController extends AbstractController
{
    #[Route("/", name: 'consulter')]
    public function consulterEtalonnages(
        DefinitionProfilComputerRepository $profil_computer_repository,
        RuntimeResourcesRepository         $runtime_resources_repository): Response
    {
        $etalonnages = $profil_computer_repository->findAll();

        $etalonnage_file_exist = array_combine(
            array_map(fn(DefinitionProfilComputer $definition) => $definition->id, $etalonnages),
            array_map(
                fn(DefinitionProfilComputer $definition) => $runtime_resources_repository->etalonnageComputerExists($definition),
                $etalonnages
            )
        );
        return $this->render('etalonnage/index.html.twig',
            ["etalonnages" => $etalonnages, "etalonnage_file_exists" => $etalonnage_file_exist]);
    }

    #[Route("/file/{id}", name: "file")]
    public function consulterEtalonnageFile(
        DefinitionProfilComputerRepository $definition_etalonnage_computer_repository,
        RuntimeResourcesRepository         $runtime_resources_repository,
        int                                $id): Response
    {
        /** @var DefinitionProfilComputer $etalonnage_computer */
        $etalonnage_computer = $definition_etalonnage_computer_repository->find($id);
        $file_path = $runtime_resources_repository->etalonnageComputerPath($etalonnage_computer);

        return $this->file($file_path, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route("/creer", name: "creer")]
    public function creerEtalonnage(
        ManagerRegistry           $doctrine,
        DefinitionScoreRepository $definition_score_repository,
        RuntimeResourcesRepository $runtime_resources_repository,
        Request                   $request
    ): Response
    {
        $etalonnage = new DefinitionProfilComputer(
            id: 0,
            score: $definition_score_repository->findAll()[0],
            nom: "",
            nom_php: ""
        );

        $form = $this->createForm(
            EtalonnageType::class,
            $etalonnage
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $doctrine->getManager()->persist($etalonnage);

            /** @var UploadedFile $file */
            $file = $form->get("file")->getData();
            $path = $runtime_resources_repository->etalonnageComputerDirectoryPath();
            $file->move($path, $file->getClientOriginalName());

            $doctrine->getManager()->flush();

            return $this->redirectToRoute("etalonnage_consulter");

        }

        return $this->render("etalonnage/creer.html.twig", ["form" => $form]);
    }

}