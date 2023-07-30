<?php

namespace App\Core\IO\Correcteur;

use App\Entity\Correcteur;
use App\Entity\Echelle;
use App\Entity\EchelleCorrecteur;
use App\Entity\Structure;
use App\Entity\Test;
use App\Repository\StructureRepository;
use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use SimpleXMLElement;

class ImportCorrecteurXML
{
    const STRUCTURE_KEY = "structure";
    const NOM_KEY = "nom";
    const TEST_KEY = "test";
    const TEST_NOM_KEY = "nom";
    const ECHELLE_KEY = "echelle";
    const ECHELLE_NOM_KEY = "nom";
    const ECHELLE_EXPRESSION_KEY = "expression";

    public function __construct(
        private readonly StructureRepository $structureRepository,
        private readonly TestRepository      $testRepository,
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

        $structure = $this->structure($correcteurXMLErrorHandler, $xml);
        $tests = $this->tests($correcteurXMLErrorHandler, $xml);

        if (!$tests || !$structure) {
            return false;
        }

        $nom = $xml[self::NOM_KEY];
        $correcteur = new Correcteur(id: 0, tests: new ArrayCollection($tests), structure: $structure, nom: $nom, echelles: new ArrayCollection());

        $echelles = $this->echelles($correcteurXMLErrorHandler, $xml, $correcteur);

        if ($echelles == null) {
            return false;
        }

        $correcteur->echelles = new ArrayCollection($echelles);

        return $correcteur;
    }

    private function structure(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, SimpleXMLElement $xml): Structure|false
    {
        $nomStructure = $xml[self::STRUCTURE_KEY];
        $structures = $this->structureRepository->findBy(["nom" => $nomStructure]);

        if (empty($structures)) {
            $correcteurXMLErrorHandler->handleError("Aucune structure n'existe au nom de $nomStructure");
            return false;
        } else if (count($structures) > 1) {
            $correcteurXMLErrorHandler->handleError("Plusieurs structures existent au nom de $nomStructure");
            return false;
        }

        return $structures[0];
    }

    /**
     * @param ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler
     * @param SimpleXMLElement $xml
     * @return Test[]|false
     */
    private function tests(ImportCorrecteurXMLErrorHandler $correcteurXMLErrorHandler, SimpleXMLElement $xml): array|false
    {
        $result = [];

        foreach ($xml->{self::TEST_KEY} as $testXml) {
            $testNom = $testXml[self::TEST_NOM_KEY];
            $test = $this->testRepository->findBy(["nom" => $testNom]);

            if (empty($test)) {
                $correcteurXMLErrorHandler->handleError("Aucun test n'existe au nom de $testXml");
                return false;
            } else if (count($test) > 1) {
                $correcteurXMLErrorHandler->handleError("Plusieurs tests existent au nom de $testXml");
                return false;
            }

            $result[] = $test[0];
        }


        return $result;
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
        foreach ($correcteur->structure->echelles as $echelle) {
            $defined_echelles[$echelle->nom_php] = false;
        }

        $valid = true;
        $echelles = [];

        /** @var SimpleXMLElement $echelle */
        foreach ($xml->{self::ECHELLE_KEY} as $echelle) {

            $nomEchelle = (string)$echelle[self::ECHELLE_NOM_KEY];

            if (key_exists($nomEchelle, $defined_echelles) && $defined_echelles[$nomEchelle]) {
                $correcteurXMLErrorHandler->handleError("L'échelle $nomEchelle est définie plusieurs fois");
                $valid = false;
            }

            $echelle_entity = $this->echelleFromProfil($correcteur->structure, $nomEchelle);

            if ($echelle_entity == null) {
                $correcteurXMLErrorHandler->handleError("Aucune echelle n'a le nom $nomEchelle dans le score_etalonne " . $correcteur->structure->nom);
                $valid = false;
            }

            $expression = $echelle[self::ECHELLE_EXPRESSION_KEY];
            $echelles[] = new EchelleCorrecteur(id: 0, expression: $expression, echelle: $echelle_entity, correcteur: $correcteur);
            $defined_echelles[$nomEchelle] = true;
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

    private function echelleFromProfil(Structure $profil, string $nom): Echelle|null
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