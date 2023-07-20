<?php

namespace App\Controller;

use App\Core\Reponses\FiltreSessionStorage;
use App\Core\Reponses\ReponsesCandidatSessionStorage;
use App\Core\Reponses\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheParameters;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\RechercheParametersType;
use App\Repository\ReponseCandidatRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/recherche", name: "recherche_")]
class RechercheController extends AbstractController
{
    const LOWEST_TIME = "@1344988800";

    #[Route("/vider", name: "vider")]
    public function vider(ReponsesCandidatStorage $reponsesCandidatStorage): Response
    {
        $reponsesCandidatStorage->set(array());
        $this->addFlash("success", "Les candidats ont été retirés, vous pouvez en sélectionner de nouveaux.");
        return $this->redirectToRoute("recherche_index");
    }

    /**
     * TODO ne pas passer par sessionStorage, mais par storage directement (encapsulation correcte)
     * @param ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage
     * @param int $reponse_id
     * @return RedirectResponse
     */
    #[Route("/deselectionner/{reponse_id}", "deselectionner")]
    public function removeReponseCandidat(
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        int                            $reponse_id): RedirectResponse
    {
        $cached_reposes = $reponsesCandidatSessionStorage->get();
        $reponsesCandidatSessionStorage->set(array_diff($cached_reposes, array($reponse_id)));
        $this->addFlash("success", "Le candidat a été retiré.");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/index", name: "index")]
    public function index(
        LoggerInterface                $logger,
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        Request                        $request,
        ReponseCandidatRepository      $reponseCandidatRepository
    ): Response
    {
        $request->get("page");

        $reponsesCount = $reponseCandidatRepository->count([]);
        $pageCount = (int)ceil($reponsesCount / RechercheParameters::PAGE_SIZE);

        $parameters = new RechercheParameters(
            filtrePrenom: "",
            filtreNom: "",
            page: 0,
            filtreDateDeNaissanceMin: new DateTime(self::LOWEST_TIME),
            filtreDateDeNaissanceMax: new DateTime("now"),
            niveauScolaire: null,
            session: null,
            checkedReponsesCandidat: []
        );

        $cachedReponsesIds = $reponsesCandidatSessionStorage->getOrSetDefault(array());
        $selectionnees = $reponseCandidatRepository->findAllByIds($cachedReponsesIds);

        $form = $this->createForm(RechercheParametersType::class, $parameters, [RechercheParametersType::OPTION_PAGE_COUNT_KEY => $pageCount]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            for ($page = 0; $page < $pageCount; $page++) {

                /** @var ClickableInterface $submitPage */
                $submitPage = $form->get(RechercheParametersType::SUBMIT_PAGE_PREFIX_KEY . $page);

                if ($submitPage->isClicked()) {
                    $logger->debug("Choix de la page " . $page);
                    $parameters->page = $page;

                    $reponsesAtPage = $reponseCandidatRepository->findAllFromParameters($parameters);

                    $checkedReponsesCandidat = array_map(
                        function (ReponseCandidat $reponseCandidat) use ($cachedReponsesIds) {
                            return new ReponseCandidatChecked($reponseCandidat,
                                in_array($reponseCandidat->id, $cachedReponsesIds));
                        },
                        $reponsesAtPage
                    );

                    $parameters->checkedReponsesCandidat = $checkedReponsesCandidat;
                    $form = $this->createForm(RechercheParametersType::class, $form->getData(), [RechercheParametersType::OPTION_PAGE_COUNT_KEY => $pageCount]);

                    return $this->render("recherche/index.html.twig", [
                        "selectionnes" => $selectionnees,
                        "form" => $form->createView()
                    ]);
                }
            }

            /** @var ClickableInterface $submitSelectionner */
            $submitSelectionner = $form->get(RechercheParametersType::SUBMIT_SELECTIONNER_KEY);

            if ($submitSelectionner->isClicked()) {

                throw new Exception();

            }

            /** @var ClickableInterface $submitFiltrer */
            $submitFiltrer = $form->get(RechercheParametersType::SUBMIT_FILTRER_KEY);

            if ($submitFiltrer->isClicked()) {
                $reponsesAtPage = $reponseCandidatRepository->findAllFromParameters($parameters);

                $checkedReponsesCandidat = array_map(
                    function (ReponseCandidat $reponseCandidat) use ($cachedReponsesIds) {
                        return new ReponseCandidatChecked($reponseCandidat,
                            in_array($reponseCandidat->id, $cachedReponsesIds));
                    },
                    $reponsesAtPage
                );

                $parameters->checkedReponsesCandidat = $checkedReponsesCandidat;

                return $this->render("recherche/index.html.twig", [
                    "selectionnes" => $selectionnees,
                    "form" => $form->createView()
                ]);
            }

            return $this->redirectToRoute("recherche_index");
        }

        $reponsesAtPage = $reponseCandidatRepository->findAllFromParameters($parameters);

        $checkedReponsesCandidat = array_map(
            function (ReponseCandidat $reponseCandidat) use ($cachedReponsesIds) {
                return new ReponseCandidatChecked($reponseCandidat,
                    in_array($reponseCandidat->id, $cachedReponsesIds));
            },
            $reponsesAtPage
        );

        $parameters->checkedReponsesCandidat = $checkedReponsesCandidat;
        $form->setData($parameters);

        return $this->render("recherche/index.html.twig", [
            "selectionnes" => $selectionnees,
            "form" => $form->createView()
        ]);
    }

}