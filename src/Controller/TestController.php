<?php

namespace App\Controller;

use App\Entity\Test;
use App\Entity\QuestionTest;
use App\Form\CreerTestType;
use App\Form\TestType;
use App\Repository\GrilleRepository;
use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/test", name: "test_")]
class TestController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        TestRepository   $testRepository,
        GrilleRepository $grilleRepository,
    ): Response
    {
        $tests = $testRepository->findAll();
        $grilles = $grilleRepository->indexToInstance();

        return $this->render("test/index.html.twig", ["tests" => $tests, "grilles" => $grilles]);
    }


    #[Route("/consulter/{id}", name: "consulter")]
    public function consulter(
        GrilleRepository $grilleRepository,
        Test             $test,
    ): Response
    {
        $grille = $grilleRepository->getFromIndex($test->index_grille);

        return $this->render("test/test.html.twig", ["test" => $test, "grille" => $grille]);
    }

    /**
     * Formulaire de création d'un test
     * @param EntityManagerInterface $entityManager
     * @param GrilleRepository $grilleRepository
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface $entityManager,
        GrilleRepository       $grilleRepository,
        Request                $request
    ): RedirectResponse|Response
    {
        $test = new Test(
            id: 0,
            nom: "",
            version_batterie: 0,
            index_grille: GrilleRepository::GRILLE_OCTOBRE_2019_INDEX,
            concours: new ArrayCollection(),
            correcteurs: new ArrayCollection(),
            sessions: new ArrayCollection(),
            questions: new ArrayCollection()
        );

        $form = $this->createForm(CreerTestType::class, $test);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $grille = $grilleRepository->getFromIndex($test->index_grille);
            for ($indiceQuestion = 1; $indiceQuestion <= $grille->nombre_questions; $indiceQuestion++) {
                $test->questions->add(new QuestionTest(
                    id: 0,
                    indice: $indiceQuestion,
                    intitule: "Q" . $indiceQuestion,
                    abreviation: "Q" . $indiceQuestion,
                    test: $test,
                    type: QuestionTest::TYPE_INUTILISE
                ));
            }

            $entityManager->persist($test);
            $entityManager->flush();

            $this->addFlash("success", "Le test a été créé. Vous pouvez maintenant paramétrer son contenu.");

            return $this->redirectToRoute("test_modifier", ["id" => $test->id]);
        }

        return $this->render("test/form_creer.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire de modification d'un test
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param Test $test
     * @return Response
     */
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface $entityManager,
        Request                $request,
        Test                   $test,
    ): Response
    {
        $form = $this->createForm(TestType::class, $test);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute("test_consulter", ["id" => $test->id]);
        }

        return $this->render("test/form_modifier.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Supprime un test
     * @param Test $test
     * @return Response
     */
    #[Route("/supprimer/confirmer/{id}", name: "supprimer_confirmer")]
    public function supprimerConfirmer(Test $test): Response
    {
        $supprimable = Test:: supprimable($test);
        return $this->render("test/supprimer.html.twig", ["test" => $test, "supprimable" => $supprimable]);
    }

    /**
     * Supprime un test
     * @param EntityManagerInterface $entityManager
     * @param Test $test
     * @return RedirectResponse
     */
    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        LoggerInterface        $logger,
        EntityManagerInterface $entityManager,
        Test                   $test): RedirectResponse
    {
        $supprimable = Test::supprimable($test);
        if ($supprimable) {
            $entityManager->remove($test);
            $entityManager->flush();

            $this->addFlash("success", "Suppression du test enregistrée.");

            return $this->redirectToRoute("test_index");
        } else {
            $logger->error("Impossible de supprimer le test", ["test" => $test]);
            $this->addFlash("danger", "Impossible de supprimer le test");

            return $this->redirectToRoute("test_supprimer_confirmer", ["id" => $test->id]);
        }
    }
}