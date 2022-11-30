<?php

namespace App\Api;

use App\Core\Entities\Reponse;
use App\Entity\CandidatReponse;
use App\Entity\CandidatScore;
use App\Entity\DefinitionScoreComputer;
use App\Entity\Session;
use App\OpenApiBundle\Api\DefaultApiInterface;
use App\OpenApiBundle\Model\ReponsesACalculer;
use App\OpenApiBundle\Model\ScoresACalculer;
use App\Repository\FilesRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CortestApi implements DefaultApiInterface
{
    private LoggerInterface $logger;
    private ManagerRegistry $doctrine;
    private FilesRepository $files_repository;

    /**
     * @param ManagerRegistry $doctrine
     * @param FilesRepository $filesRepository
     * @param LoggerInterface $logger
     */
    public function __construct(ManagerRegistry $doctrine, FilesRepository $filesRepository, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->files_repository = $filesRepository;
        $this->logger = $logger;
    }

    public function calculerReponsesPost(?ReponsesACalculer $reponsesACalculer,
                                         int                &$responseCode,
                                         array              &$responseHeaders): void
    {
        $serializer = SerializerBuilder::create()->build();
        $manager = $this->doctrine->getManager();

        /** @var Session $session */
        $session = $manager->find(Session::class, $reponsesACalculer->getSessionID());

        $reponses = $this->files_repository->getReponsesFromRaw($session->grille, $reponsesACalculer->getReponses());

        if ($reponses == null) {
            $this->logger->debug("Echec de la traduction des réponses");
            $responseCode = Response::HTTP_CONFLICT;
        } else {
            /** @var Reponse $reponse */
            foreach ($reponses as $reponse) {
                $manager->persist(
                    new CandidatReponse(0, $session, $serializer->serialize($reponse, "json"))
                );
            }

            $manager->flush();

            $responseCode = Response::HTTP_NO_CONTENT;
        }
    }

    public function calculerScoresPost(?ScoresACalculer $scoresACalculer,
                                       int              &$responseCode,
                                       array            &$responseHeaders): void
    {
        $manager = $this->doctrine->getManager();
        $repository = $manager->getRepository(CandidatReponse::class);

        /** @var DefinitionScoreComputer $score_computer */
        $score_computer = $manager->find(DefinitionScoreComputer::class, $scoresACalculer->getScoreComputerID());

        $candidat_reponses = $repository->createQueryBuilder("cr")
            ->where("cr.id IN (:ids)")
            ->setParameter("ids", $scoresACalculer->getReponses())
            ->getQuery()
            ->execute();

        $candidat_reponses_serialized = array_map(function (CandidatReponse $candidatReponse) {
            return $candidatReponse->reponses;
        }, $candidat_reponses);

        $this->logger->debug("Fetched reponse entities");

        $reponses = $this->files_repository->getReponsesFromSerialized($score_computer->grille,
            $candidat_reponses_serialized);


        if ($reponses != null) {

            $this->logger->debug("Translated into reponse objects");

            $scores_serialized = $this->files_repository->getScoresFromReponses($score_computer, $reponses);

            if ($scores_serialized != null) {

                $this->logger->debug("Computed score from responses");

                for ($i = 0; $i < count($scores_serialized); $i++) {

                    $manager->persist(
                        new CandidatScore(0, $candidat_reponses[$i], $score_computer, $scores_serialized[$i])
                    );

                }

                $manager->flush();

                $responseCode = Response::HTTP_OK;
                return;
            }
        }

        $responseCode = Response::HTTP_NO_CONTENT;
    }
}