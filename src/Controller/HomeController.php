<?php

namespace App\Controller;

use App\Repository\ResourceRepository;
use App\Security\DeleteResourceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: "home")]
    public function index(
        Security           $security,
        ResourceRepository $resourceRepository
    ): Response
    {
        $resources = $resourceRepository->findAll();

        $deletable = [];
        foreach ($resources as $resource) {
            $deletable[$resource->id] = $security->isGranted(DeleteResourceVoter::DELETE, $resource);
        }

        return $this->render('home.html.twig', ["resources" => $resources, "deletable" => $deletable]);
    }
}