<?php

namespace App\Core\IO\Correcteur;

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
    const CORRECTEUR_KEY = "correcteur";
    const PROFIL_KEY = "profil";
    const NOM_KEY = "nom";
    const CONCOURS_KEY = "concours";
    const ECHELLES_KEY = "echelles";
    const ECHELLE_KEY = "echelle";
    const ECHELLE_NOM_KEY = "nom";
    const ECHELLE_EXPRESSION_KEY = "expression";

    public function __construct(
        private readonly ProfilRepository   $profilRepository,
        private readonly ConcoursRepository $concoursRepository,
    )
    {
    }

    public function load(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, string $content): Correcteur|false
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);

        $errors = libxml_get_errors();
        if (!empty($errors)) {
            foreach (libxml_get_errors() as $error) {
                $correcteurXMLErrorHandler->handleError($error->message, $error->line, $error->column);
            }
            return false;
        }

        $profil = $this->profil($correcteurXMLErrorHandler, $xml);
        $concours = $this->concours($correcteurXMLErrorHandler, $xml);

        if (!$concours || !$profil) {
            return false;
        }
        $nom = $xml->{self::NOM_KEY};
        $correcteur = new Correcteur(id: 0, concours: $concours, profil: $profil, nom: $nom, echelles: new ArrayCollection());

        $echelles = $this->echelles($correcteurXMLErrorHandler, $xml, $correcteur);

        if ($echelles == null) {
            return false;
        }

        $correcteur->echelles = new ArrayCollection($echelles);

        return $correcteur;
    }

    private function profil(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, SimpleXMLElement $xml): Profil|false
    {
        $nom_profil = $xml->{self::PROFIL_KEY};
        $profils = $this->profilRepository->findBy(["nom" => $nom_profil]);

        if (empty($profils)) {
            $correcteurXMLErrorHandler->handleError("Aucun profil n'existe au nom de $nom_profil");
            return false;
        } else if (count($profils) > 1) {
            $correcteurXMLErrorHandler->handleError("Plusieurs profils existent au nom de $nom_profil");
            return false;
        }

        return $profils[0];
    }

    private function concours(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, SimpleXMLElement $xml): Concours|false
    {
        $nom_concours = $xml->{self::CONCOURS_KEY};
        $concours = $this->concoursRepository->findBy(["nom" => $nom_concours]);

        if (empty($concours)) {
            $correcteurXMLErrorHandler->handleError("Aucun concours n'existe au nom de $nom_concours");
            return false;
        } else if (count($concours) > 1) {
            $correcteurXMLErrorHandler->handleError("Plusieurs concours existent au nom de $nom_concours");
            return false;
        }

        return $concours[0];
    }

    /**
     * @param ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler
     * @param SimpleXMLElement $xml
     * @param Correcteur $correcteur
     * @return EchelleCorrecteur[]|false
     */
    private function echelles(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, SimpleXMLElement $xml, Correcteur $correcteur): array|false
    {
        $defined_echelles = [];
        /** @var Echelle $echelle */
        foreach ($correcteur->profil->echelles as $echelle) {
            $defined_echelles[$echelle->nom_php] = false;
        }

        $valid = true;
        $echelles = [];

        /** @var SimpleXMLElement $echelle */
        foreach ($xml->{self::ECHELLES_KEY}->{self::ECHELLE_KEY} as $echelle) {

            $nom_echelle = (string)$echelle->{self::ECHELLE_NOM_KEY};

            if (key_exists($nom_echelle, $defined_echelles) && $defined_echelles[$nom_echelle]) {
                $correcteurXMLErrorHandler->handleError("L'échelle $nom_echelle est définie plusieurs fois");
                $valid = false;
            }

            $echelle_entity = $this->echelleFromProfil($correcteur->profil, $nom_echelle);

            if ($echelle_entity == null) {
                $correcteurXMLErrorHandler->handleError("Aucune echelle n'a le nom $nom_echelle dans le profil " . $correcteur->profil->nom);
                $valid = false;
            }

            $expression = $echelle->{self::ECHELLE_EXPRESSION_KEY};
            $echelles[] = new EchelleCorrecteur(id: 0, expression: $expression, echelle: $echelle_entity, correcteur: $correcteur);
            $defined_echelles[$nom_echelle] = true;
        }

        foreach ($defined_echelles as $nom => $defined) {
            if (!$defined) {
                $correcteurXMLErrorHandler->handleError("L'échelle $nom n'est pas définie dans le fichier");
                $valid = false;
            }
        }

        if (!$valid) {
            return false;
        } else {
            return $echelles;
        }
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