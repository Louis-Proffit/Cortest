<?php

namespace App\Core;

use App\Core\Entities\Grille;
use PHPStan\BetterReflection\Reflection\Exception\ClassDoesNotExist;
use PHPStan\BetterReflection\Reflection\Exception\FunctionDoesNotExist;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class GrilleToProfil
{
    public static function compute(string $batterie_file_name, string $batterie_class_name, Grille $grille)
    {
        if (file_exists($batterie_file_name)) {
            require_once $batterie_file_name;
            if (class_exists($batterie_class_name)) {
                $batterie = $batterie_class_name($grille);
                if (method_exists($batterie, "compute")) {
                    return $batterie->compute();
                } else {
                    throw new FunctionDoesNotExist();
                }
            } else {
                throw new ClassDoesNotExist();
            }
        } else {
            throw new FileNotFoundException();
        }
    }
}