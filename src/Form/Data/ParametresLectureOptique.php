<?php

namespace App\Form\Data;

use App\Entity\Session;
use Symfony\Component\Validator\Constraints\Json;

class ParametresLectureOptique
{
    public Session $session;
    #[Json]
    public int $questions;
    
}