<?php

namespace App\Core\Res\ProfilOuScore;

use App\Core\Res\Property;

/**
 * Liste des données suceptibles d'apparaître sur une feuille de profil
 */
interface ProfilOuScore
{
    function getNom();

    /**
     * @return Property[]
     */
    function getProperties():array;

    function generateEtalonnageValues(int $nombre_de_classes):array;

    function generateCorrecteurValues():array;
}