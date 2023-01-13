<?php

namespace App\Core;

class Property
{
    public string $nom;
    public string $nom_php;

    /**
     * @param string $nom
     * @param string $nom_php
     */
    public function __construct(string $nom, string $nom_php)
    {
        $this->nom = $nom;
        $this->nom_php = $nom_php;
    }


}