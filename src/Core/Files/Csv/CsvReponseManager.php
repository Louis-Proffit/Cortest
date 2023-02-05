<?php

namespace App\Core\Files\Csv;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvReponseManager
{
    public function __construct(
        private readonly CsvManager $csv_manager
    )
    {
    }

    /**
     * @param ReponseCandidat[] $reponses
     * @param string $file_name
     * @return BinaryFileResponse
     */
    public function export(array $reponses, string $file_name): BinaryFileResponse
    {

        $data = [];

        foreach ($reponses as $reponse) {
            $toAdd = [
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Niveau scolaire" => $reponse->niveau_scolaire->nom,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
                "Sexe" => match ($reponse->sexe) {
                    ReponseCandidat::INDEX_HOMME => "Homme",
                    default => "Femme"
                },
                "Réservé" => $reponse->reserve,
                "Autre 1" => $reponse->autre_1,
                "Autre 2" => $reponse->autre_2,
                "Code barre" => $reponse->code_barre,
            ];

            $index = 1;
            foreach ($reponse->reponses as $value) {
                $toAdd["Réponse " . $index] = $value;
                $index++;
            }

            $data[] = $toAdd;
        }

        return $this->csv_manager->export($data, $file_name);
    }
}