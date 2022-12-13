<?php

namespace Res;

use App\Core\Entities\ProfilOuScore;

class ProfilOuScoreCahierDesCharges extends ProfilOuScore
{
    public $collationnement;
    public $verbal_mot;
    public $spatial;
    public $verbal_syntaxique;
    public $raisonnement;
    public $dic;
    public $anxiete;
    public $irritabilite;
    public $impusilvite;
    public $introspection;
    public $entetement;
    public $mefiance;
    public $depression;
    public $gene;
    public $manque_altruisme;
    public $sociabilite;
    public $spontaneite;
    public $ascendance;
    public $assurance;
    public $interet_intelletuel;
    public $nouveaute;
    public $creativite;
    public $rigueur;
    public $planification;
    public $perseverance;
    public $sincerite;
    public $obsessionalite;
    public $agressivite;
    public $depressivite;
    public $paranoidie;
    public $narcissisme;
    public $intolerance_a_la_frustration;

    /**
     * @param $collationnement
     * @param $verbal_mot
     * @param $spatial
     * @param $verbal_syntaxique
     * @param $raisonnement
     * @param $dic
     * @param $anxiete
     * @param $irritabilite
     * @param $impusilvite
     * @param $introspection
     * @param $entetement
     * @param $mefiance
     * @param $depression
     * @param $gene
     * @param $manque_altruisme
     * @param $sociabilite
     * @param $spontaneite
     * @param $ascendance
     * @param $assurance
     * @param $interet_intelletuel
     * @param $nouveaute
     * @param $creativite
     * @param $rigueur
     * @param $planification
     * @param $perseverance
     * @param $sincerite
     * @param $obsessionalite
     * @param $agressivite
     * @param $depressivite
     * @param $paranoidie
     * @param $narcissisme
     * @param $intolerance_a_la_frustration
     */
    public function __construct($collationnement,
                                $verbal_mot,
                                $spatial,
                                $verbal_syntaxique,
                                $raisonnement,
                                $dic,
                                $anxiete,
                                $irritabilite,
                                $impusilvite,
                                $introspection,
                                $entetement,
                                $mefiance,
                                $depression,
                                $gene,
                                $manque_altruisme,
                                $sociabilite,
                                $spontaneite,
                                $ascendance,
                                $assurance,
                                $interet_intelletuel,
                                $nouveaute,
                                $creativite,
                                $rigueur,
                                $planification,
                                $perseverance,
                                $sincerite,
                                $obsessionalite,
                                $agressivite,
                                $depressivite,
                                $paranoidie,
                                $narcissisme,
                                $intolerance_a_la_frustration)
    {
        $this->collationnement = $collationnement;
        $this->verbal_mot = $verbal_mot;
        $this->spatial = $spatial;
        $this->verbal_syntaxique = $verbal_syntaxique;
        $this->raisonnement = $raisonnement;
        $this->dic = $dic;
        $this->anxiete = $anxiete;
        $this->irritabilite = $irritabilite;
        $this->impusilvite = $impusilvite;
        $this->introspection = $introspection;
        $this->entetement = $entetement;
        $this->mefiance = $mefiance;
        $this->depression = $depression;
        $this->gene = $gene;
        $this->manque_altruisme = $manque_altruisme;
        $this->sociabilite = $sociabilite;
        $this->spontaneite = $spontaneite;
        $this->ascendance = $ascendance;
        $this->assurance = $assurance;
        $this->interet_intelletuel = $interet_intelletuel;
        $this->nouveaute = $nouveaute;
        $this->creativite = $creativite;
        $this->rigueur = $rigueur;
        $this->planification = $planification;
        $this->perseverance = $perseverance;
        $this->sincerite = $sincerite;
        $this->obsessionalite = $obsessionalite;
        $this->agressivite = $agressivite;
        $this->depressivite = $depressivite;
        $this->paranoidie = $paranoidie;
        $this->narcissisme = $narcissisme;
        $this->intolerance_a_la_frustration = $intolerance_a_la_frustration;
    }


}