<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpticScannerController extends AbstractController
{

    #[Route("/scanner", name: "scanner_route")]
    function scanner(Request $request): Response
    {
        return $this->render("scanner/scanner.html.twig");
    }

}