<?php

namespace App\Form\Data;

use App\Constraint\ClassName;
use App\Core\Renderer\Renderer;
use App\Core\Renderer\RendererRepository;
use App\Entity\Profil;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Isin;
use Symfony\Component\Validator\Constraints\NotBlank;

class GraphiqueCreer
{
    #[NotBlank]
    public string $nom;

    #[Choice(choices: RendererRepository::INDEX)]
    public int $renderer_index;

    public Profil $profil;

    /**
     * @param string $nom
     * @param int $renderer_index
     * @param Profil $profil
     */
    public function __construct(string $nom, int $renderer_index, Profil $profil)
    {
        $this->nom = $nom;
        $this->renderer_index = $renderer_index;
        $this->profil = $profil;
    }


}