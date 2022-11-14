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
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OpticScannerController extends AbstractController
{
    private $fd;
    private $logger;

    /**
     * @param $fd
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    #[Route('opticScanner/open', methods: ['POST'])]
    public function open(Request $request): Response
    {
        $this->fd = dio_open("COM1", O_RDWR);
        $this->logger->info("Opening com port");
        return $this->redirectToRoute('scanner');
    }

    #[Route('opticScanner/close', methods: ['POST'])]
    public function close(Request $request): Response
    {
        if ($this->fd != null) {
            dio_close($this->fd);
        }
        $this->logger->info("Closing com port");
        return $this->redirectToRoute('scanner');
    }


    #[Route('/opticScanner', methods: ['GET', 'HEAD'])]
    public function form(Request $request): Response
    {
        return $this->render('optic_scanner.html.twig');
    }
}