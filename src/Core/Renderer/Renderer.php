<?php

namespace App\Core\Renderer;

use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleGraphique;
use App\Entity\Etalonnage;
use App\Entity\ReponseCandidat;
use Symfony\Component\Form\FormTypeInterface;
use Twig\Environment;

interface Renderer
{
    public function render(
        Environment     $environment,
        ReponseCandidat $reponse,
        Correcteur      $correcteur,
        Etalonnage      $etalonnage,
        array           $options,
        array           $echelleOptions,
        array           $etalonnageParameters,
        array           $score,
        array           $profil,
        array           $typeEchelle,
        array           $arborescence,
    ): string;

    /**
     * @return RendererOption[]
     */
    public function getOptions(): array;

    /**
     * @return RendererOption[]
     */
    public function getEchelleOptions(): array;

    public function initializeEchelleOption(Echelle $echelle): array;

    public function initializeOptions(): array;

    public function getNom(): string;

}