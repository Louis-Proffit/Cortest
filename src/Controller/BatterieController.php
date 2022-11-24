<?php

namespace App\Controller;

use App\Entity\Batterie;
use App\Repository\FilesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Hoa\File\File;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;
use function App\list_files;

class BatterieController extends AbstractController
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route("/api/batterie", name: "list_batteries", methods: ['GET', 'HEAD'])]
    public function fetch(ManagerRegistry $doctrine): Response
    {
        $batteries = $doctrine->getRepository(Batterie::class)->findAll();
        return $this->json($batteries);
    }

    #[Route("/batterie", name: "create_batterie_form", methods: ['GET', 'HEAD'])]
    public function get_create_batterie_form(): Response
    {
        $grilles = FilesRepository::findAllGrilles(getcwd(), $this->getParameter("res.grilles.dir"));

        return $this->render("create_batterie_form.html.twig", [
            "grilles" => $grilles
        ]);
    }

    #[Route("/batterie", name: "create_batterie", methods: ['POST'])]
    public function create_batterie(Request $request, ManagerRegistry $doctrine): Response
    {
        $grille = $request->request->get("grille");
        $batterie = $request->request->get("batterie");

        $manager = $doctrine->getManager();

        $manager->persist(new Batterie(0, $grille, $batterie));
        $manager->flush();

        return $this->redirectToRoute("create_batterie_form");
    }
}