<?php

namespace App\Core\Exception;

use App\Core\IO\Pdf\Compiler\LatexCompilationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Prend en charge les exceptions d'échec de compilation Latex, en redirigeant le client vers une page qui indique la source de l'erreur et le contenu du fichier de log associé.
 * @see LatexCompilationFailedException
 */
#[AsEventListener]
final class LatexCompilationFailedExceptionEventListener extends AbstractController
{

    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($throwable instanceof LatexCompilationFailedException) {
            $this->logger->critical("Echec de compilation latex récupérée après l'erreur suivante : " . $throwable);

            if($throwable->logFilePath != null) {
                $logContent = file_get_contents($throwable->logFilePath);
                $this->logger->critical("Contenu du log de compilation : " . $logContent);
            } else {
                $logContent = "Pas de log pour la compilation";
                $this->logger->critical("Pas de fichier de log produit");
            }

            $event->setResponse($this->render("renderer/echec_compilation.html.twig", ["exception" => $throwable, "log" => $logContent]));
        }
    }
}