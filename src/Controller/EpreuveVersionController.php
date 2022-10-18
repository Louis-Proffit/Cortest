<?php

// src/Controller/EpreuveVersionController.php
namespace App\Controller;

use App\Entity\EpreuveNotationDirecte;
use App\Entity\EpreuveVersion;
use App\Repository\EpreuveRepository;
use App\Repository\EpreuveVersionRepository;
use App\Repository\EpreuveNotationDirecteRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EpreuveVersionController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/creationVersion', methods: ['GET', 'HEAD'])]
    public function show(Request $request): Response
    {
        $epreuveRepo = new EpreuveRepository($this->doctrine);
        $epreuves = $epreuveRepo->findAll();

        // 'pathNextPage' is the name of the route to the next page
        return $this->render('epreuve/choixEpreuve.html.twig', ['epreuves' => $epreuves, 'pathNextPage' => 'creationVersion']);
    }

    #[Route('/creationVersion/{idEpreuve}', name:'creationVersion')]
    public function creationVersion(Request $request, int $idEpreuve): Response
    {
        $repoEpreuve = new EpreuveRepository($this->doctrine);
        $epreuve = $repoEpreuve->findOneBy(['code'=> $idEpreuve]);

        $version = new EpreuveVersion(epreuve: $epreuve);
        $choices = ['choices' => ['Non Requis' => 0, 'Optionnel' => 1, 'Requis' => 2]];
        $form = $this->createFormBuilder($version)
            ->add('version', IntegerType::class)
            ->add('descriptif', TextType::class)
            ->add('nom', ChoiceType::class, $choices)
            ->add('prenom', ChoiceType::class, $choices)
            ->add('nomJeuneFille', ChoiceType::class, $choices)
            ->add('niveauScolaire', ChoiceType::class, $choices)
            ->add('naissance', ChoiceType::class, $choices)
            ->add('sexe', ChoiceType::class, $choices)
            ->add('concours', ChoiceType::class, $choices)
            ->add('sgap', ChoiceType::class, $choices)
            ->add('dateExamen', ChoiceType::class, $choices)
            ->add('typeConcours', ChoiceType::class, $choices)
            ->add('versionBatterie', ChoiceType::class, $choices)
            ->add('reserve', ChoiceType::class, $choices)
            ->add('champ1', ChoiceType::class, $choices)
            ->add('champ2', ChoiceType::class, $choices)
            ->add('codeBarre', ChoiceType::class, $choices)
            ->add('save', SubmitType::class, ['label' => 'Créer Épreuve'])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $version = $form->getData();
            $version->setStatut(0);

            $repo = new EpreuveVersionRepository($this->doctrine);
            $repo->save($version, true);


            return $this->renderForm('epreuve/_requeteReussie.html.twig', [
                'id' => $version->getEpreuve()->getCode(),
            ]);
        }
        return $this->renderForm('epreuve/_new.html.twig', [
            'form' => $form,
        ]);
    }
}

?>