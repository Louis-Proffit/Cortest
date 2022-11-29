<?php

namespace App\Core;

use PHPStan\BetterReflection\Reflection\Exception\ClassDoesNotExist;
use PHPStan\BetterReflection\Reflection\Exception\FunctionDoesNotExist;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Computation
{
    public static function computeResults(string $file_path, string $batterie_class_name, string $grille)
    {
        if (file_exists($file_path)) {
            require_once $file_path;
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