<?php

namespace App\Controller;

use App\Entity\Echelle;
use App\Form\EchelleType;
use App\Repository\EchelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(self::BASE_PATH, name: self::BASE_ROUTE)]
class EchelleController extends AbstractCrudController
{

    const BASE_ROUTE = "echelle_";
    const BASE_PATH = "/echelle";

    public function __construct(EchelleRepository $echelle_repository)
    {
        parent::__construct($echelle_repository, EchelleType::class, self::BASE_ROUTE . "index");
    }


    protected function produce(): Echelle
    {
        return new Echelle(
            id: 0,
            nom: "",
            nom_php: "", type: Echelle::TYPE_ECHELLE_SIMPLE
        );
    }

    #[Route("/creation/{id}", name: "creation")]
    public function creation(Request $request,
                             EntityManagerInterface $entityManager,
                             int $id,
    ): Response
    {
        $echelle = $this->produce();

        $form = $this->createForm(EchelleType::class, $echelle);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $echelle = $form->getData();
            $entityManager->persist($echelle);
            $entityManager->flush();

            return $this->redirectToRoute('correcteur_ajoutEchelle', ['id' => $id]);
        }

        return $this->render('echelle/creer.html.twig', [
            'form' => $form,
        ]);

    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_echelle.html.twig", ["items" => $items]);
    }
}