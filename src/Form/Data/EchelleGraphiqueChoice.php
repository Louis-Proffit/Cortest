<?php

namespace App\Form\Data;

use App\Entity\EchelleGraphique;
use Symfony\Component\Validator\Constraints\NotNull;

class EchelleGraphiqueChoice
{

    #[NotNull]
    public EchelleGraphique $echelle;

    /**
     * @param EchelleGraphique $echelle
     */
    public function __construct(EchelleGraphique $echelle)
    {
        $this->echelle = $echelle;
    }


}