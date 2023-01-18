<?php

namespace App\Form\Data;

use App\Entity\Session;
use Symfony\Component\Validator\Constraints\Json;

class ParametresLectureJSON
{
    public Session $session;
    #[Json]
    public string $contents;
}