<?php

namespace App\Core\Import;

use App\Entity\Concours;
use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\Profil;
use App\Repository\ConcoursRepository;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleXMLElement;

class ImportCorrecteurXML
{

    public function __construct(
        private readonly ProfilRepository   $profilRepository,
        private readonly ConcoursRepository $concoursRepository
    )
    {
    }

    /**
     * @throws ImportCorrecteurXMLException
     */
    public function load(string $content): Correcteur
    {
        $xml = simplexml_load_string($content);

        $profil = $this->profil($xml);
        $concours = $this->concours($xml);
        $nom = $xml->nom;

        $correcteur = new Correcteur(id: 0, concours: $concours, profil: $profil, nom: $nom, echelles: new ArrayCollection());

        $echelles = $this->echelles($xml, $correcteur);

        $correcteur->echelles = new ArrayCollection($echelles);

        return $correcteur;
    }

    /**
     * @throws ImportCorrecteurXMLException
     */
    private function profil(SimpleXMLElement $xml): Profil
    {
        $nom_profil = $xml->profil;
        $profils = $this->profilRepository->findBy(["nom" => $nom_profil]);

        if (empty($profils)) {
            throw new ImportCorrecteurXMLException("Aucun profil n'existe au nom de $nom_profil");
        } else if (count($profils) > 1) {
            throw new ImportCorrecteurXMLException("Plusieurs profils existent au nom de $nom_profil");
        }

        return $profils[0];
    }

    /**
     * @throws ImportCorrecteurXMLException
     */
    private function concours(SimpleXMLElement $xml): Concours
    {
        $nom_concours = $xml->concours;
        $concours = $this->concoursRepository->findBy(["nom" => $nom_concours]);

        if (empty($concours)) {
            throw new ImportCorrecteurXMLException("Aucun concours n'existe au nom de $nom_concours");
        } else if (count($concours) > 1) {
            throw new ImportCorrecteurXMLException("Plusieurs concours existent au nom de $nom_concours");
        }

        return $concours[0];
    }

    /**
     * @param SimpleXMLElement $xml
     * @param Correcteur $correcteur
     * @return EchelleCorrecteur[]
     * @throws ImportCorrecteurXMLException
     */
    private function echelles(SimpleXMLElement $xml, Correcteur $correcteur): array
    {
        $defined_echelles = [];
        /** @var Echelle $echelle */
        foreach ($correcteur->profil->echelles as $echelle) {
            $defined_echelles[$echelle->nom_php] = false;
        }

        $echelles = [];

        /** @var SimpleXMLElement $echelle */
        foreach ($xml->echelles->echelle as $echelle) {

            $nom_echelle = (string)$echelle->echelle;

            if (key_exists($nom_echelle, $defined_echelles) && $defined_echelles[$nom_echelle]) {
                throw new ImportCorrecteurXMLException("L'échelle $nom_echelle est définie plusieurs fois");
            }

            $echelle_entity = $this->echelleFromProfil($correcteur->profil, $nom_echelle);

            if ($echelle_entity == null) {
                throw new ImportCorrecteurXMLException("Aucune echelle n'a le nom $nom_echelle dans le profil " . $correcteur->profil->nom);
            }

            $expression = $echelle->expression;
            $echelles[] = new EchelleCorrecteur(id: 0, expression: $expression, echelle: $echelle_entity, correcteur: $correcteur);
            $defined_echelles[$nom_echelle] = true;
        }

        foreach ($defined_echelles as $nom => $defined) {
            if (!$defined) {
                throw new ImportCorrecteurXMLException("L'échelle $nom n'est pas définie dans le fichier");
            }
        }

        return $echelles;
    }

    private function echelleFromProfil(Profil $profil, string $nom): Echelle|null
    {
        /** @var Echelle $echelle */
        foreach ($profil->echelles as $echelle) {
            if ($echelle->nom_php === $nom) {
                return $echelle;
            }
        }

        return null;
    }
}