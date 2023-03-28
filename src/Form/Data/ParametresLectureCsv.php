<?php

namespace App\Form\Data;

use App\Entity\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ParametresLectureCsv
{
    public Session $session;
    public UploadedFile $contents;
}