<?php

namespace App\Core\Renderer;

class RendererOption
{
    public string $nom;
    public string $nom_php;
    public mixed $default;
    public string $form_type;

    /**
     * @param string $nom
     * @param string $nom_php
     * @param mixed $default
     * @param string $form_type
     */
    public function __construct(string $nom, string $nom_php, mixed $default, string $form_type)
    {
        $this->nom = $nom;
        $this->nom_php = $nom_php;
        $this->default = $default;
        $this->form_type = $form_type;
    }


}