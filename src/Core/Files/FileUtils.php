<?php

namespace App\Core\Files;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class FileUtils
{

    const CONTENT_DISPOSITION = "Content-Disposition";

    public static function setFileResponseFileName(Response $response, string $fileName): void
    {

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set(self::CONTENT_DISPOSITION, $disposition);
    }

}