<?php

namespace App\Controller;

use App\Core\Activite\LogEntryProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/activite", name: "activite_")]
class ActiviteController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        LogEntryProcessor $logEntryProcessor
    ): Response
    {
        $logs = $logEntryProcessor->findAll();
        return $this->render("activite/index.html.twig", ["logs" => $logs]);
    }
}