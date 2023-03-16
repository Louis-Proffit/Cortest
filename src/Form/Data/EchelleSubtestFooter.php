<?php

namespace App\Form\Data;

use App\Entity\EchelleGraphique;
use App\Entity\Subtest;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotNull;

class EchelleSubtestFooter
{

    #[NotNull]
    public EchelleGraphique $echelle;

    #[Choice(choices: Subtest::TYPES_FOOTERS)]
    public int $type;

    /**
     * @param EchelleGraphique $echelle
     * @param int $type
     */
    public function __construct(EchelleGraphique $echelle, int $type)
    {
        $this->echelle = $echelle;
        $this->type = $type;
    }


}