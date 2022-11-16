<?php

// src/Controller/EpreuveController.php
namespace App\Controller;

use App\Entity\Epreuve;
use App\Entity\EpreuveEchelleSimple;
use App\Entity\EpreuveNotationDirecte;
use App\Entity\EpreuveVersion;
use App\template;
use App\Repository\EpreuveRepository;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use TypeError;

class OpticScannerController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route("/scanner", name: "scanner_route")]
    function scanner(Request $request): Response
    {
        return $this->render("scanner/scanner.html.twig");
    }

}