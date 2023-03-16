<?php

namespace App\Core\Renderer\Values;

use App\Core\Renderer\Renderer;
use App\Core\Renderer\RendererOption;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleGraphique;
use App\Entity\Etalonnage;
use App\Entity\Graphique;
use App\Entity\ReponseCandidat;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RendererBatonnets implements Renderer
{

    const OPTION_TITRE = "Titre";
    const OPTION_TITRE_PHP = "titre";
    const OPTION_SOUS_TITRE = "Sous-titre";
    const OPTION_SOUS_TITRE_PHP = "sous_titre";

    /** @var RendererOption[] */
    private array $options;

    /** @var RendererOption[] */
    private array $echelleOptions;

    private string $imagesDirectory;

    // private array $etalonnageParameters;

    public function __construct()
    {
        $this->options = [
            new RendererOption(nom: self::OPTION_TITRE,
                nom_php: self::OPTION_TITRE_PHP,
                default: "Bilan psycotechnique",
                form_type: TextType::class),
            new RendererOption(nom: self::OPTION_SOUS_TITRE,
                nom_php: self::OPTION_SOUS_TITRE_PHP,
                default: "BSR2P",
                form_type: TextType::class)
        ];
        $this->echelleOptions = [
            new RendererOption(nom: EchelleGraphique::OPTION_NOM_AFFICHAGE,
                nom_php: EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP, default: "", form_type: TextType::class)
        ];
        /*$this->etalonnageParameters = [
            new RendererOption(nom: "nombreClasses",
                nom_php: "nombre_classes",
                default: 10,
                form_type: IntegerType::class)
        ];*/
        $this->imagesDirectory = str_replace("\\", "/", getCwd()) . "/../templates/renderer/images/";
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(
        Environment     $environment,
        ReponseCandidat $reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        Graphique       $graphique,
        array           $score,
        array           $profil,
        array           $options,
        array           $optionsEchelle,
    ): string
    {

        return $environment->render("renderer/batonnet_cahier_des_charges.tex.twig", [
            "reponse" => $reponse,
            "etalonnage" => $etalonnage,
            "session" => $reponse->session,
            "options" => $options,
            "optionsEchelle" => $optionsEchelle,
            "score" => $score,
            "profil" => $profil,
            "graphique" => $graphique,
            "imagesDirectory" => $this->imagesDirectory,
        ]);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getNom(): string
    {
        return "Profil en batônnets";
    }

    public function initializeOptions(): array
    {
        $result = [];

        foreach ($this->options as $option) {
            $result[$option->nom_php] = $option->default;
        }

        return $result;
    }

    public function getEchelleOptions(): array
    {
        return $this->echelleOptions;
    }

    public function initializeEchelleOption(Echelle $echelle): array
    {
        $result = [];

        foreach ($this->echelleOptions as $option) {
            $result[$option->nom_php] = $option->default;
        }

        // Pour le nom affiché, on fait un cas particulier : on initialise au nom de l'échelle
        $result[EchelleGraphique::OPTION_NOM_AFFICHAGE_PHP] = $echelle->nom;

        return $result;
    }
}