<?php

namespace App\Controller;

use App\Core\Recherche\ParametreRechercheStorage;
use App\Core\ReponseCandidat\ReponsesCandidatSessionStorage;
use App\Entity\ReponseCandidat;
use App\Form\Data\ParametresRecherche;
use App\Form\Data\CheckableReponsesCandidatWrapper;
use App\Form\ParametresRechercheType;
use App\Form\ReponsesCandidatCheckedType;
use App\Repository\ReponseCandidatRepository;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/recherche", name: "recherche_")]
class RechercheController extends AbstractController
{

    public function __construct(
        private readonly ReponseCandidatRepository      $reponseCandidatRepository,
        private readonly ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage
    )
    {
    }


    const LOWEST_TIME = "@1344988800";

    #[Route("/vider", name: "vider")]
    public function vider(): Response
    {
        $this->reponsesCandidatSessionStorage->set([]);
        $this->addFlash("success", "Les candidats ont été retirés, vous pouvez en sélectionner de nouveaux.");
        return $this->redirectToRoute("recherche_index");
    }

    /**
     * @param int $reponse_id
     * @return RedirectResponse
     */
    #[Route("/deselectionner/{reponse_id}", "deselectionner")]
    public function removeReponseCandidat(int $reponse_id): RedirectResponse
    {
        $checkedReponsesIds = $this->reponsesCandidatSessionStorage->getOrSetDefault([]);
        $this->reponsesCandidatSessionStorage->set(array_diff($checkedReponsesIds, [$reponse_id]));
        $this->addFlash("success", "Le candidat a été désélectionné.");
        return $this->redirectToRoute("recherche_index");
    }

    /**
     * Route atteinte par le clic sur le bouton de sélection.
     * Ajoute les réponses sélectionnées éventuellement nouvelles, et redirige vers l'index.
     * @param ParametreRechercheStorage $parametreRechercheStorage
     * @param Request $request
     * @return RedirectResponse
     */
    #[Route("/selectionner", name: "selectionner")]
    public function selectionner(
        ParametreRechercheStorage $parametreRechercheStorage,
        Request                   $request
    ): RedirectResponse
    {
        $parametresRecherche = $parametreRechercheStorage->getOrSetDefault($this->getDefaultParametresRecherche());

        $checkedReponsesIds = $this->reponsesCandidatSessionStorage->getOrSetDefault([]);

        $reponsesCandidatWrapper = $this->getCheckableReponsesCandidat(
            parametresRecherche: $parametresRecherche,
            checkedReponsesIds: $checkedReponsesIds
        );
        $form = $this->formReponsesCandidats($reponsesCandidatWrapper);

        $form->handleRequest($request);

        foreach ($reponsesCandidatWrapper->checked as $id => $checked) {
            if ($checked && !in_array($id, $checkedReponsesIds)) {
                $checkedReponsesIds[] = $id;
            }
        }

        $this->reponsesCandidatSessionStorage->set($checkedReponsesIds);

        return $this->redirectToRoute("recherche_index");
    }


    #[Route("/index", name: "index")]
    public function index(
        ParametreRechercheStorage $parametreRechercheStorage,
        ReponseCandidatRepository $reponseCandidatRepository,
        LoggerInterface           $logger,
        Request                   $request
    ): Response
    {
        $parametresRecherche = $parametreRechercheStorage->getOrSetDefault($this->getDefaultParametresRecherche());

        $pageCount = $this->pageCount();
        $form = $this->formParametres($parametresRecherche, $pageCount);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var ClickableInterface $reset */
            $reset = $form->get(ParametresRechercheType::SUBMIT_RESET_KEY);

            if ($reset->isClicked()) {
                $parametreRechercheStorage->set($this->getDefaultParametresRecherche());
                return $this->redirectToRoute("recherche_index");
            }

            for ($page = 0; $page < $pageCount; $page++) {

                /** @var ClickableInterface $submitPage */
                $submitPage = $form->get(ParametresRechercheType::SUBMIT_PAGE_PREFIX_KEY . $page);

                if ($submitPage->isClicked()) {
                    $logger->debug("Choix de la page " . $page);
                    $parametresRecherche->page = $page;
                }
            }

            $parametreRechercheStorage->set($parametresRecherche);
        }

        $checkedReponsesCandidatIds = $this->reponsesCandidatSessionStorage->getOrSetDefault([]);
        $checkedReponsesCandidat = $reponseCandidatRepository->findAllByIds($checkedReponsesCandidatIds);

        $reponsesCandidatWrapper = $this->getCheckableReponsesCandidat($parametresRecherche, $checkedReponsesCandidatIds);

        $formReponsesCandidats = $this->formReponsesCandidats(
            $reponsesCandidatWrapper
        );

        $reponsesCandidat = $this->reponseCandidatRepository->findAllByIdsIndexById(array_keys($reponsesCandidatWrapper->checked));

        return $this->renderIndexForm(
            formParametres: $form,
            formReponsesCandidats: $formReponsesCandidats,
            reponsesCandidat: $reponsesCandidat,
            checkedReponsesCandidat: $checkedReponsesCandidat
        );
    }

    /**
     * @param FormInterface $formParametres
     * @param FormInterface $formReponsesCandidats
     * @param ReponseCandidat[] $reponsesCandidat
     * @param ReponseCandidat[] $checkedReponsesCandidat
     * @return Response
     */
    private function renderIndexForm(
        FormInterface $formParametres,
        FormInterface $formReponsesCandidats,
        array         $reponsesCandidat,
        array         $checkedReponsesCandidat
    ): Response
    {

        return $this->render("recherche/index.html.twig", [
            "formReponsesCandidat" => $formReponsesCandidats->createView(),
            "formParametres" => $formParametres->createView(),
            "reponsesCandidat" => $reponsesCandidat,
            "checkedReponsesCandidat" => $checkedReponsesCandidat
        ]);
    }

    private function pageCount(): int
    {
        $reponsesCount = $this->reponseCandidatRepository->count([]);
        return (int)ceil($reponsesCount / ParametresRecherche::PAGE_SIZE);
    }

    private function getCheckableReponsesCandidat(
        ParametresRecherche $parametresRecherche,
        array               $checkedReponsesIds
    ): CheckableReponsesCandidatWrapper
    {
        $ids = $this->reponseCandidatRepository->findAllIdsFromParameters($parametresRecherche);

        $result = [];

        foreach ($ids as $id => $_) {
            $result[$id] = in_array($id, $checkedReponsesIds);
        }

        return new CheckableReponsesCandidatWrapper($result);
    }

    /**
     * @return FormInterface
     */
    private function formReponsesCandidats(
        CheckableReponsesCandidatWrapper $reponsesCandidatWrapper,
    ): FormInterface
    {
        return $this->createForm(
            ReponsesCandidatCheckedType::class,
            $reponsesCandidatWrapper
        );
    }

    private
    function formParametres(
        ParametresRecherche $parametres,
        int                 $pageCount
    ): FormInterface
    {
        return $this->createForm(
            ParametresRechercheType::class,
            $parametres,
            [ParametresRechercheType::OPTION_PAGE_COUNT_KEY => $pageCount]
        );
    }

    private function getDefaultParametresRecherche(): ParametresRecherche
    {
        return new ParametresRecherche(
            filtrePrenom: "",
            filtreNom: "",
            page: 0,
            filtreDateDeNaissanceMin: new DateTime(self::LOWEST_TIME),
            filtreDateDeNaissanceMax: (new DateTime("now"))->add(new DateInterval("P1D")),
            dateSession: null,
            niveauScolaire: null,
            session: null
        );
    }
}