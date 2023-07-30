<?php

namespace App\Core\IO\Correcteur;

use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use DOMDocument;
use SimpleXMLElement;

class ExportCorrecteurXML
{
    public function export(Correcteur $correcteur): string|false
    {
        $xml = new SimpleXMLElement("<correcteur/>");

        foreach ($correcteur->tests as $test) {
            $testXml = $xml->addChild(ImportCorrecteurXML::TEST_KEY);
            $testXml->addAttribute(ImportCorrecteurXML::TEST_NOM_KEY, $test->nom);
        }

        $xml->addAttribute(ImportCorrecteurXML::STRUCTURE_KEY, $correcteur->structure->nom);
        $xml->addAttribute(ImportCorrecteurXML::NOM_KEY, $correcteur->nom);

        /** @var EchelleCorrecteur $echelle */
        foreach ($correcteur->echelles as $echelle) {
            $echelleXml = $xml->addChild(ImportCorrecteurXML::ECHELLE_KEY);
            $echelleXml->addAttribute(ImportCorrecteurXML::ECHELLE_NOM_KEY, $echelle->echelle->nom_php);
            $echelleXml->addAttribute(ImportCorrecteurXML::ECHELLE_EXPRESSION_KEY, $echelle->expression);
        }


        return $this->prettify($xml);
    }

    private function prettify(SimpleXMLElement $xml): string
    {
        $domxml = new DOMDocument();
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml->asXML());
        return $domxml->saveXML();
    }

}