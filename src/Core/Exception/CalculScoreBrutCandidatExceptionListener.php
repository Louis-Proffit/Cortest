<?php

namespace App\Core\Exception;

use App\Core\IO\Pdf\Compiler\LatexCompilationFailedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Prend en charge les exceptions d'échec de compilation Latex, en redirigeant le client vers une page qui indique la source de l'erreur et le contenu du fichier de log associé.
 * @see LatexCompilationFailedException
 */
#[AsEventListener]
final class CalculScoreBrutCandidatExceptionListener extends AbstractController
{

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($throwable instanceof CalculScoreBrutCandidatException) {

            $this->addFlash("danger", $this->getMessage($throwable));
            $event->setResponse($this->redirectToRoute("home"));

        }
    }

    private function getMessage(CalculScoreBrutCandidatException $e): string
    {
        return "Pour le candidat " . $e->reponseCandidat->prenom . " " . $e->reponseCandidat->nom . " : " . $e->getPrevious()->getMessage();
    }
}