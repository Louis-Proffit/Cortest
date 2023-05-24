<?php

namespace App\Controller\Exception;

use App\Core\Reponses\DifferentSessionException;
use App\Core\Reponses\NoReponsesCandidatException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Prend en charge les exceptions lancées dans le cas ou les réponses candidats traitées représentent plusieurs sessions ou aucune (pas de réponses candidat)
 * Cela peut-être le cas lors de calculs de scores ou de profils
 */
#[AsEventListener]
final class DifferentSessionExceptionEventListener extends AbstractController
{

    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($throwable instanceof DifferentSessionException) {

            $this->logger->info("Prise en charge de l'exception $throwable");

            $this->addFlash("danger", "Les réponses candidat sélectionnées représentent plusieurs sessions : " . $throwable->sessionNameDisplay());
            $event->setResponse($this->redirectToRoute("recherche_index"));

        } else if ($throwable instanceof NoReponsesCandidatException) {

            $this->logger->info("Prise en charge de l'exception $throwable");

            $this->addFlash("danger", "Pas de réponses candidat sélectionnées, impossible d'effectuer le traitement");
            $event->setResponse($this->redirectToRoute("recherche_index"));
        }
    }
}