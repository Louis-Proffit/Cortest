<?php

namespace App\Form\Data;

use App\Entity\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ParametresLectureCsv
{
    public Session $session;
    public UploadedFile $contents;
}