<?php

namespace App\Controller;

use App\Core\ReponseCandidat\ReponsesCandidatSessionStorage;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Form\Data\RechercheParameters;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\RechercheParametersType;
use App\Repository\ReponseCandidatRepository;
use DateTime;
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
        $cached_reponses = $reponsesCandidatSessionStorage->getOrSetDefault([]);
        $reponsesCandidatSessionStorage->set(array_diff($cached_reponses, array($reponse_id)));
        $this->addFlash("success", "Le candidat a été retiré.");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/index", name: "index")]
    public function index(
        ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage,
        ReponseCandidatRepository      $reponseCandidatRepository,
        LoggerInterface                $logger,
        Request                        $request
    ): Response
    {
        $page = (int)$request->get("page");
        $cachedReponsesIds = $reponsesCandidatSessionStorage->getOrSetDefault(array());

        $reponsesCount = $reponseCandidatRepository->count([]);
        $pageCount = (int)ceil($reponsesCount / RechercheParameters::PAGE_SIZE);

        $parameters = new RechercheParameters(
            filtrePrenom: "",
            filtreNom: "",
            page: $page,
            filtreDateDeNaissanceMin: new DateTime(self::LOWEST_TIME),
            filtreDateDeNaissanceMax: new DateTime("now"),
            dateSession: null,
            niveauScolaire: null,
            session: null,
            checkedReponsesCandidat: []
        );

        $reponsesAtPage = $reponseCandidatRepository->findAllFromParameters($parameters);
        $checkedReponsesAtPage = $this->reponsesToCheckedReponses($reponsesAtPage, $cachedReponsesIds);
        $parameters->checkedReponsesCandidat = $checkedReponsesAtPage;

        $form = $this->createForm(RechercheParametersType::class, $parameters, [RechercheParametersType::OPTION_PAGE_COUNT_KEY => $pageCount]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            for ($page = 0; $page < $pageCount; $page++) {

                /** @var ClickableInterface $submitPage */
                $submitPage = $form->get(RechercheParametersType::SUBMIT_PAGE_PREFIX_KEY . $page);

                if ($submitPage->isClicked()) {
                    $logger->debug("Choix de la page " . $page);
                    $parameters->page = $page;

                    return $this->renderIndexForm(
                        reponseCandidatRepository: $reponseCandidatRepository,
                        parameters: $parameters,
                        cachedReponsesIds: $cachedReponsesIds,
                        pageCount: $pageCount
                    );
                }
            }

            /** @var ClickableInterface $submitSelectionner */
            $submitSelectionner = $form->get(RechercheParametersType::SUBMIT_SELECTIONNER_KEY);

            if ($submitSelectionner->isClicked()) {

                foreach ($parameters->checkedReponsesCandidat as $reponseCandidatChecked) {
                    if ($reponseCandidatChecked->checked && !in_array($reponseCandidatChecked->reponse_candidat->id, $cachedReponsesIds)) {
                        $cachedReponsesIds[] = $reponseCandidatChecked->reponse_candidat->id;
                    }
                }

                $reponsesCandidatSessionStorage->set($cachedReponsesIds);
                return $this->renderIndexForm(
                    reponseCandidatRepository: $reponseCandidatRepository,
                    parameters: $parameters,
                    cachedReponsesIds: $cachedReponsesIds,
                    pageCount: $pageCount
                );
            }

            /** @var ClickableInterface $submitFiltrer */
            $submitFiltrer = $form->get(RechercheParametersType::SUBMIT_FILTRER_KEY);

            if ($submitFiltrer->isClicked()) {
                return $this->renderIndexForm(
                    reponseCandidatRepository: $reponseCandidatRepository,
                    parameters: $parameters,
                    cachedReponsesIds: $cachedReponsesIds,
                    pageCount: $pageCount
                );
            }

            $this->addFlash("danger", "Une erreur est survenue");
            $logger->error("Unreachable");
        }

        return $this->renderIndexForm(
            reponseCandidatRepository: $reponseCandidatRepository,
            parameters: $parameters,
            cachedReponsesIds: $cachedReponsesIds,
            pageCount: $pageCount
        );
    }

    private function renderIndexForm(
        ReponseCandidatRepository $reponseCandidatRepository,
        RechercheParameters       $parameters,
        array                     $cachedReponsesIds,
        int                       $pageCount): Response
    {
        $selectionnes = $reponseCandidatRepository->findAllByIds($cachedReponsesIds);

        $reponsesAtPage = $reponseCandidatRepository->findAllFromParameters($parameters);

        $checkedReponsesCandidat = $this->reponsesToCheckedReponses($reponsesAtPage, $cachedReponsesIds);

        $parameters->checkedReponsesCandidat = $checkedReponsesCandidat;

        $form = $this->createForm(RechercheParametersType::class, $parameters, [RechercheParametersType::OPTION_PAGE_COUNT_KEY => $pageCount]);

        return $this->render("recherche/index.html.twig", [
            "selectionnes" => $selectionnes,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param ReponseCandidat[] $reponsesAtPage
     * @param int[] $cachedReponsesIds
     * @return ReponseCandidatChecked[]
     */
    private function reponsesToCheckedReponses(array $reponsesAtPage, array $cachedReponsesIds): array
    {
        return array_map(
            function (ReponseCandidat $reponseCandidat) use ($cachedReponsesIds) {
                return new ReponseCandidatChecked($reponseCandidat,
                    in_array($reponseCandidat->id, $cachedReponsesIds));
            },
            $reponsesAtPage
        );
    }

}