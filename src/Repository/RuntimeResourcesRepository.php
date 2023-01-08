<?php

namespace App\Repository;

use App\Core\Entities\EtalonnageComputer;
use App\Core\Entities\GrilleReponse;
use App\Core\Entities\ScoreComputer;
use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScore;
use App\Entity\DefinitionScoreComputer;
use Psr\Log\LoggerInterface;

class RuntimeResourcesRepository
{
    private LoggerInterface $logger;

    private string $definition_grilles_reponses_folder;
    private string $definition_profil_ou_score_folder;
    private string $score_computer_folder;
    private string $etalonnage_computer_folder;

    /**
     * @param LoggerInterface $logger
     * @param string $definition_grilles_reponses_folder
     * @param string $definition_profil_ou_score_folder
     * @param string $score_computer_folder
     * @param string $etalonnage_computer_folder
     */
    public function __construct(LoggerInterface $logger, string $definition_grilles_reponses_folder, string $definition_profil_ou_score_folder, string $score_computer_folder, string $etalonnage_computer_folder)
    {
        $this->logger = $logger;
        $this->definition_grilles_reponses_folder = $definition_grilles_reponses_folder;
        $this->definition_profil_ou_score_folder = $definition_profil_ou_score_folder;
        $this->score_computer_folder = $score_computer_folder;
        $this->etalonnage_computer_folder = $etalonnage_computer_folder;
    }

    public function definitionScoreOuProfilExists(DefinitionScore $definition): bool
    {
        return $this->fileExists(getcwd(), $this->definition_profil_ou_score_folder, $definition->nom_php);
    }

    public function definitionGrilleReponsesExists(DefinitionGrille $definition): bool
    {
        return $this->fileExists(getcwd(), $this->definition_grilles_reponses_folder, $definition->nom_php);
    }

    public function definitionGrille(DefinitionGrille $definition_grille): GrilleReponse
    {
        return $this->requireAndGetInstance(getcwd(),
            $this->definition_grilles_reponses_folder,
            $definition_grille->nom_php);
    }

    public function scoreComputerExists(DefinitionScoreComputer $definition): bool
    {
        return $this->fileExists(getcwd(), $this->score_computer_folder, $definition->nom_php);
    }

    public function scoreComputer(DefinitionScoreComputer $definition): ScoreComputer
    {
        return $this->requireAndGetInstance(getcwd(), $this->score_computer_folder, $definition->nom_php);
    }

    public function etalonnageComputerDirectoryPath(): string
    {
        return $this->directoryPath(getcwd(), $this->etalonnage_computer_folder);
    }

    public function etalonnageComputerPath(DefinitionProfilComputer $definition): string
    {
        return $this->path(getcwd(), $this->etalonnage_computer_folder, $definition->nom_php);
    }

    public function etalonnageComputerExists(DefinitionProfilComputer $definition): bool
    {
        return $this->fileExists(getcwd(), $this->etalonnage_computer_folder, $definition->nom_php);
    }

    public function etalonnageComputer(DefinitionProfilComputer $definition): EtalonnageComputer
    {
        return $this->requireAndGetInstance(getcwd(), $this->etalonnage_computer_folder, $definition->nom_php);
    }

    private function requireAndGetInstance(string $root_folder, string $res_folder, string $nom_php)
    {
        $path = $this->path($root_folder, $res_folder, $nom_php);

        $this->logger->debug("require_once on path " . $path);
        require_once $path;

        return new ($this->className($res_folder, $nom_php))();
    }

    private function className(string $res_folder, string $nom_php): string
    {
        return "\\Res\\" . $res_folder . "\\" . $nom_php;
    }

    private function path(string $root_folder, string $specific_folder, string $nom_php): string
    {
        return $this->directoryPath($root_folder, $specific_folder) . "/" . $nom_php . ".php";
    }

    private function directoryPath(string $root_folder, $specific_folder): string
    {
        return $root_folder . "/Res/" . $specific_folder;
    }

    private function fileExists(string $root_folder, string $specific_folder, string $nom_php): bool
    {
        $path = $this->path($root_folder, $specific_folder, $nom_php);
        $result = file_exists($path);
        if (!$result) {
            $this->logger->info("File " . $path . " doesn't exist");
        }
        return $result;
    }
}