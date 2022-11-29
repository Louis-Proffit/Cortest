<?php

// src/Controller/EpreuveEchelleController.php
namespace App\Controller;

use App\Entity\EpreuveEchelle;
use App\Repository\EpreuveRepository;
use App\Repository\EpreuveVersionRepository;
use App\Repository\EpreuveNotationDirecteRepository;
use App\Repository\EpreuveEchelleRepository;

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

class EpreuveEchelleController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/creationEchelle')]
    public function choixEpreuveEchelle(Request $request): Response
    {
        $epreuveRepo = new EpreuveRepository($this->doctrine);
        $epreuves = $epreuveRepo->findAll();
        return $this->render('epreuve/choixEpreuve.html.twig', ['epreuves' => $epreuves, 'pathNextPage' => 'choixVersionEchelle']);
    }

    #[Route('/creationEchelle/{idEpreuve}', name: 'choixVersionEchelle')]
    public function choixVersionEchelle(Request $request, int $idEpreuve): Response
    {
        $epreuveRepo = new EpreuveRepository($this->doctrine);
        $epreuve = $epreuveRepo->findOneBy(['code' => $idEpreuve]);
        $versions = $epreuve->getVersions();
        return $this->render('epreuve/choixVersion.html.twig', ['versions' => $versions, 'pathNextPage' => 'choixTypeEchelle' ]);
    }

    #[Route('/creationEchelle/{idEpreuve}/{idVersion}', name: 'choixTypeEchelle')]
    public function choixTypeEchelle(Request $request, int $idEpreuve, int $idVersion): Response
    {
        $epreuveRepo = new EpreuveRepository($this->doctrine);
        $epreuve = $epreuveRepo->findOneBy(['code' => $idEpreuve]);
        $version = $epreuve->getVersion($idVersion);
        return $this->render('epreuve/choixEchelle.html.twig', ['pathNextPage' => 'creationEchelle', 'version' => $version ]);
    }

    #[Route('/creationEchelle/{idEpreuve}/{idVersion}/{typeEchelle}', name:'creationEchelle')]
    public function creationEchelle(Request $request, int $idEpreuve, int $idVersion, int $typeEchelle): Response
    {
        $repoEpreuve = new EpreuveRepository($this->doctrine);
        $epreuve = $repoEpreuve->findOneBy(['code' => $idEpreuve]);
        $version = $epreuve->getVersion($idVersion);

        $echelle = new EpreuveEchelle(version: $version, type: $typeEchelle);
        $choiceTrueFalse = ['choices' => ['Vrai' => true, 'Faux' => false]];
        $choiceTrueFalseNull = ['choices' => ['Null' => null, 'Vrai' => true, 'Faux' => false]];
        $form = $this->createFormBuilder($epreuve)
            ->add('numQuestion', IntegerType::class)
            ->add('intitule', TextType::class, array(
                'attr' => ['maxlength' => 50]))
            ->add('abreviation', TextType::class, array(
                'attr' => ['maxlength' => 10]))
            ->add('repA', NumberType::class)
            ->add('boolA', ChoiceType::class, $choiceTrueFalse)
            ->add('repB', NumberType::class)
            ->add('boolB', ChoiceType::class, $choiceTrueFalse)
            ->add('repC', NumberType::class)
            ->add('boolC', ChoiceType::class, $choiceTrueFalseNull)
            ->add('repD', NumberType::class)
            ->add('boolD', ChoiceType::class, $choiceTrueFalseNull)
            ->add('repE', NumberType::class)
            ->add('boolE', ChoiceType::class, $choiceTrueFalseNull)
            ->add('noRep', NumberType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer Question'])
            ->getForm();
        
        $form->handleRequest($request);

        $repo = new EpreuveNotationDirecteRepository($this->doctrine);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            $repo->save($question, true);

            $previousQuestions = $repo->findByVersionFields($version);

            return $this->renderForm('epreuve/creationNotationDirecte.html.twig', [
                'previousQuestions' => $previousQuestions,
                'form' => $form,
                'idEpreuve' => $idEpreuve,
                'idVersion' => $idVersion,
            ]);
        }

        $previousQuestions = $repo->findByVersionFields($version);

        return $this->renderForm('epreuve/creationNotationDirecte.html.twig', [
            'previousQuestions' => $previousQuestions,
            'form' => $form,
            'idEpreuve' => $idEpreuve,
            'idVersion' => $idVersion,
        ]);
    }
}

?>