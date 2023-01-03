<?php

namespace App\Controller;

use App\Core\Entities\EtalonnageComputer;
use App\Entity\DefinitionEtalonnageComputer;
use App\Repository\RuntimeResourcesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class EtalonnageController extends AbstractController
{
    #[Route("/etalonnages", name: 'consulter_etalonnages')]
    public function consulterEtalonnages(ManagerRegistry $doctrine, RuntimeResourcesRepository $runtime_resources_repository)
    {
        $etalonnages = $doctrine->getRepository(DefinitionEtalonnageComputer::class)->findAll();


        $etalonnage_file_exist = array_combine(
            array_map(fn(DefinitionEtalonnageComputer $definition) => $definition->id, $etalonnages),
            array_map(
                fn(DefinitionEtalonnageComputer $definition) => $runtime_resources_repository->etalonnageComputerExists($definition),
                $etalonnages
            )
        );
        return $this->render('etalonnages.html.twig',
            ["etalonnages" => $etalonnages, "etalonnage_file_exists" => $etalonnage_file_exist]);
    }

    #[Route("/etalonnage/file/{id}", name: "consulter_etalonnage_file")]
    public function consulterEtalonnageFile(ManagerRegistry $doctrine, RuntimeResourcesRepository $runtime_resources_repository, int $id)
    {
        /** @var DefinitionEtalonnageComputer $etalonnage_computer */
        $etalonnage_computer = $doctrine->getManager()->find(DefinitionEtalonnageComputer::class, $id);
        $file_path = $runtime_resources_repository->etalonnageComputerPath($etalonnage_computer);

        return $this->file($file_path, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

}