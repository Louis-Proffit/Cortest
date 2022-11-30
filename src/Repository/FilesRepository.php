<?php

namespace App\Repository;

use App\Core\Entities\Reponse;
use App\Core\Entities\ProfilOuScore;
use App\Core\Entities\EtalonnageComputer;
use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScoreComputer;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;

class FilesRepository
{
    private LoggerInterface $logger;

    private string $definition_grille_folder;
    private string $definition_score_folder;
    private string $score_computer_folder;
    private string $etalonnage_computer_folder;

    /**
     * @param string $definition_grille_folder
     * @param string $definition_score_folder
     * @param string $score_computer_folder
     * @param string $etalonnage_computer_folder
     * @param LoggerInterface $logger
     */
    public function __construct(string          $definition_grille_folder,
                                string          $definition_score_folder,
                                string          $score_computer_folder,
                                string          $etalonnage_computer_folder,
                                LoggerInterface $logger)
    {
        $this->definition_grille_folder = $definition_grille_folder;
        $this->definition_score_folder = $definition_score_folder;
        $this->score_computer_folder = $score_computer_folder;
        $this->etalonnage_computer_folder = $etalonnage_computer_folder;
        $this->logger = $logger;
    }

    public function getScoresFromReponses(DefinitionScoreComputer $scoreComputer, array $reponses): array|null
    {
        $serializer = SerializerBuilder::create()->build();

        if ($this->loadClass($this->score_computer_folder, $scoreComputer->nom_php)) {

            /** @var EtalonnageComputer $computer */
            $computer = new $scoreComputer->nom_php();

            return array_map(
                function ($reponse) use ($serializer, $computer) {
                    return $serializer->serialize($computer->compute($reponse), "json");
                },
                $reponses
            );
        }

        return null;
    }

    public function getReponsesFromRaw(DefinitionGrille $definition, array $raws): array|null
    {

        if ($this->loadClass($this->definition_grille_folder, $definition->nom_php)) {
            return array_map(
                function (string $raw) use ($definition) {
                    /** @var Reponse $reponse */
                    $reponse = new $definition->nom_php();
                    $reponse->fill($raw);
                    return $reponse;
                },
                $raws
            );
        }

        return null;
    }

    public function getReponsesFromSerialized(DefinitionGrille $definition, array $serialized): array|null
    {
        $serializer = SerializerBuilder::create()->build();

        if ($this->loadClass($this->definition_grille_folder, $definition->nom_php)) {
            return array_map(
                function (string $serialized) use ($serializer, $definition) {
                    return $serializer->deserialize($serialized, $definition->nom_php, "json");
                },
                $serialized
            );
        }

        return null;
    }

    public function getScoreFromReponse(DefinitionScoreComputer $scoreComputer,
                                        Reponse                 $reponse): string|null
    {
        $score = $this->getScoresFromReponses($scoreComputer, [$reponse]);
        if ($score != null) {
            return $score[0];
        }
        return null;
    }

    public function getReponseFromSerialized(DefinitionGrille $definition, string $serialized): array|null
    {
        $response = $this->getReponsesFromSerialized($definition, [$serialized]);
        if ($response != null) {
            return $response[0];
        }
        return null;
    }

    public function getReponseFromRaw(DefinitionGrille $definition, string $raw): Reponse|null
    {
        $response = $this->getReponsesFromRaw($definition, [$raw])[0];
        if ($response != null) {
            return $response[0];
        }
        return null;
    }

    private function loadClass(string $folder, string $nom_php): bool
    {

        $file = $folder . "/" . $nom_php . ".php";

        $this->logger->debug("Loading file " . $file);

        if (file_exists($file)) {

            require_once $file;

            if (class_exists($nom_php)) {

                return true;

            } else {
                $this->logger->debug("Class " . $nom_php . " doesn't exist");
            }
        } else {
            $this->logger->debug("File doesn't exist");
        }
        return false;
    }

}