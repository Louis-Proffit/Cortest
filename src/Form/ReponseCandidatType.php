<?php

namespace App\Form;

use App\Entity\ReponseCandidat;
use App\Repository\NiveauScolaireRepository;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReponseCandidatType extends AbstractType
{

    public function __construct(
        private readonly NiveauScolaireRepository $niveau_scolaire_repository,
        private readonly SessionRepository        $session_repository,
    )
    {
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add("session", ChoiceType::class, [
            "choices" => $this->session_repository->choices()])
            ->add("nom", TextType::class)
            ->add("prenom", TextType::class)
            ->add("nom_jeune_fille", TextType::class, ["empty_data" => ""])
            ->add("niveau_scolaire", ChoiceType::class, [
                "choices" => $this->niveau_scolaire_repository->choices()
            ])
            ->add("date_de_naissance", CortestDateType::class)
            ->add("sexe", ChoiceType::class, [
                "choices" => [
                    "Homme" => ReponseCandidat::INDEX_HOMME,
                    "Femme" => ReponseCandidat::INDEX_FEMME
                ]
            ])
            ->add("reserve", TextType::class, ["empty_data" => ""])
            ->add("autre_1", TextType::class, ["empty_data" => ""])
            ->add("autre_2", TextType::class, ["empty_data" => ""])
            ->add("code_barre", IntegerType::class)
            ->add("reponses", TextType::class, [
                "getter" => function (ReponseCandidat $reponse_candidat) {
                    /** @noinspection PhpRedundantOptionalArgumentInspection */
                    # Ne pas enlever separator, même si l'ide le propose !
                    return implode(separator: "", array: $reponse_candidat->reponses);
                },
                "setter" => function (ReponseCandidat $reponse_candidat, string $value) {
                    $reponse_candidat->reponses = array_map("intval", str_split(string: $value));
                }
            ])
            ->add("submit", SubmitType::class, ["label" => "Valider"]);
    }

}