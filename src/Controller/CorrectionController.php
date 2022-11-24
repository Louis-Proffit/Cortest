<?php

namespace App\Controller;

use App\Core\Entities\FonctionProfil;
use App\Entity\Batterie;
use App\Entity\EpreuveCandidat;
use App\Entity\ProfilCandidat;
use App\Entity\Session;
use App\Repository\FilesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function _PHPStan_582a9cb8b\React\Async\series;

class CorrectionController extends AbstractController
{

    #[Route("/session/create", name: "create_session_form", methods: ['GET'])]
    public function create_session_form(ManagerRegistry $doctrine)
    {
        $grilles = FilesRepository::findAllGrilles(getcwd(), $this->getParameter("res.grilles.dir"));

        return $this->render("create_session.html.twig", [
            "grilles" => $grilles
        ]);
    }

    #[Route("/session/create", name: "create_session", methods: ['POST'])]
    public function create_session(Request $request, ManagerRegistry $doctrine)
    {
        $session = json_decode($request->getContent());
        $manager = $doctrine->getManager(Session::class);

        $manager->persist($session);
        $manager->flush();

        return new Response("", Response::HTTP_OK);
    }

    #[Route("/correction/all", name: "all_sessions", methods: ['GET'])]
    public function get_correction_form_all_sessions(ManagerRegistry $doctrine): Response
    {
        return $this->render('all_corrections.html.twig');
    }

    #[Route("/correction/{id}", name: "all_sessions", methods: ['GET'])]
    public function get_correction_form_with_session(ManagerRegistry $doctrine, int $id): Response
    {
        $session = $doctrine->getRepository(Session::class)->find($id);

        if (!is_null($session)) {
            $epreuve_candidats = $session->getCandidats();

            return $this->render('correction.html.twig', [
                'epreuve_candidats' => $epreuve_candidats
            ]);
        } else {
            return new Response("La session demandée n'existe pas", Response::HTTP_I_AM_A_TEAPOT);
        }
    }


    #[Route("/correction", name: "correction_route", methods: ['POST'])]
    public function generate_profil_candidats(Request $request, ManagerRegistry $doctrine): Response
    {
        /**
         * @var $profil_candidat_request ProfilCandidatsRequest
         * @var $batterie Batterie
         */
        $profil_candidat_request = json_decode($request->getContent());

        $batterie_repository = $doctrine->getRepository(Batterie::class);
        $epreuve_candidat_manager = $doctrine->getRepository(EpreuveCandidat::class);
        $profil_candidat_manager = $doctrine->getManager(ProfilCandidat::class);

        $batterie = $batterie_repository->find($profil_candidat_request->getBatterieId());
        $profil_function = FilesRepository::getFonctionProfilClass(getcwd(), $this->getParameter("res.fonctionsprofil.dir"), $batterie->getFonctionProfil());

        $epreuve_candidats = $epreuve_candidat_manager->createQueryBuilder('e')
            ->where('p.id IN (:ids)')
            ->setParameter(":ids", $profil_candidat_request->getIds())
            ->getQuery()
            ->execute();

        $profil_candidats = $this->compute_profil_candidats($profil_function, $epreuve_candidats);

        foreach ($profil_candidats as $profil_candidat) {
            $profil_candidat_manager->persist($profil_candidat);
        }

        $profil_candidat_manager->flush();

        return new Response("", Response::HTTP_OK);
    }

    public function compute_profil_candidats(FonctionProfil $fonction_profil, array $epreuve_candidats): array
    {
        return array_map(function ($epreuve_candidat) use ($fonction_profil) {
            new ProfilCandidat(
                0,
                $epreuve_candidat,
                json_encode($fonction_profil->compute($epreuve_candidat->getResponses()))
            );
        }, $epreuve_candidats);
    }
}

