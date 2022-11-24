<?php

namespace App\Repository;

use function App\list_files;

class FilesRepository
{
    public static function getFonctionProfilClass(string $cwd, string $fonction_profil_directory, string $function)
    {
        $file = $cwd . $fonction_profil_directory . '\\' . $function;
        require_once($file);
        return new $file();
    }

    public static function findAllGrilles(string $cwd, string $grille_directory): array
    {
        $dir = $cwd . $grille_directory;
        return list_files($dir);
    }
}