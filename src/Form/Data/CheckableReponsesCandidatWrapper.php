<?php

namespace App\Form\Data;

use App\Form\ReponsesCandidatCheckedType;

/**
 * Classe de données du formulaire {@link ReponsesCandidatCheckedType }
 */
class CheckableReponsesCandidatWrapper
{
    /**
     * Array de type [id => checked, ...]
     * @var bool[]
     */
    public array $checked;

    /**
     * @param bool[] $checked
     */
    public function __construct(array $checked)
    {
        $this->checked = $checked;
    }


}