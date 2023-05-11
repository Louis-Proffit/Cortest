<?php

namespace App\Core\IO\Correcteur;

use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use SimpleXMLElement;

class ExportCorrecteurXML
{
    public function export(Correcteur $correcteur): string|false
    {
        $xml = new SimpleXMLElement("<correcteur/>");
        $xml->addChild(ImportCorrecteurXML::CONCOURS_KEY, $correcteur->concours->nom);
        $xml->addChild(ImportCorrecteurXML::PROFIL_KEY, $correcteur->profil->nom);
        $xml->addChild(ImportCorrecteurXML::NOM_KEY, $correcteur->nom);

        $echelles = $xml->addChild(ImportCorrecteurXML::ECHELLES_KEY);
        /** @var EchelleCorrecteur $echelle */
        foreach ($correcteur->echelles as $echelle) {
            $echelleXml = $echelles->addChild(ImportCorrecteurXML::ECHELLE_KEY);
            $echelleXml->addChild(ImportCorrecteurXML::ECHELLE_NOM_KEY, $echelle->echelle->nom_php);
            $echelleXml->addChild(ImportCorrecteurXML::ECHELLE_EXPRESSION_KEY, $echelle->expression);
        }

        return $xml->asXML();
    }

}