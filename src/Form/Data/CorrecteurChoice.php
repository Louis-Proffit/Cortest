<?php

namespace App\Form\Data;

use App\Entity\Correcteur;
use Symfony\Component\Validator\Constraints\Valid;

class CorrecteurChoice
{
    #[Valid]
    public Correcteur $correcteur;
}