<?php

namespace App\Form;

use App\Form\Generic\CortestDateType;
use App\Repository\NiveauScolaireRepository;
use App\Repository\SessionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametresRechercheType extends AbstractType
{
    const OPTION_PAGE_COUNT_KEY = "page_count";
    const SUBMIT_PAGE_PREFIX_KEY = "submit_page_";

    public function __construct(
        private readonly SessionRepository        $sessionRepository,
        private readonly NiveauScolaireRepository $niveauScolaireRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("filtrePrenom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtreNom", TextType::class, ["empty_data" => "", "required" => false])
            ->add("filtreDateDeNaissanceMin", CortestDateType::class)
            ->add("filtreDateDeNaissanceMax", CortestDateType::class)
            ->add("niveauScolaire", ChoiceType::class, [
                "choices" => $this->niveauScolaireRepository->nullable_choices()
            ])
            ->add("dateSession", CortestDateType::class, [
                "required" => false,
            ])
            ->add("session", ChoiceType::class, [
                "choices" => $this->sessionRepository->nullable_choices()
            ])
            ->add("submit", SubmitType::class, ["label" => "Filtrer"]);

        /** @var int $pageCount */
        $pageCount = $builder->getOption(self::OPTION_PAGE_COUNT_KEY);

        for ($page = 0; $page < $pageCount; $page++) {
            if ($page == $builder->getData()->page) {
                $class = "btn btn-primary";
            } else {
                $class = "btn btn-secondary";
            }
            $builder->add(self::SUBMIT_PAGE_PREFIX_KEY . $page, SubmitType::class, ["label" => "" . ($page + 1) . "/" . $pageCount, "attr" => ["class" => $class]]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define(self::OPTION_PAGE_COUNT_KEY);
        $resolver->setRequired(self::OPTION_PAGE_COUNT_KEY);
        $resolver->setAllowedTypes(self::OPTION_PAGE_COUNT_KEY, "int");
    }
}