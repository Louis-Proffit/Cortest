<?php

// src/Controller/EpreuveController.php
namespace App\Controller;

use App\Entity\Epreuve;
use App\Entity\EpreuveEchelleSimple;
use App\Entity\EpreuveNotationDirecte;
use App\Entity\EpreuveVersion;
use App\template;
use App\Repository\EpreuveRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EpreuveController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/creationEpreuve', methods: ['GET', 'HEAD'])]
    public function show(Request $request): Response
    {
        return $this->render('epreuve/default.html.twig');
    }

    public function form(Request $request): Response
    {
        $epreuve = new Epreuve();
        $form = $this->createFormBuilder($epreuve)
            ->add('code', IntegerType::class)
            ->add('etiquette', TextType::class)
            ->add('descriptif', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer Épreuve'])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $epreuve = $form->getData();

            $repo = new EpreuveRepository($this->doctrine);
            $repo->save($epreuve, true);


            return $this->renderForm('epreuve/_requeteReussie.html.twig', [
                'id' => $epreuve->getCode(),
            ]);
        }
        return $this->renderForm('epreuve/_new.html.twig', [
            'form' => $form,
        ]);
    }
}

?>