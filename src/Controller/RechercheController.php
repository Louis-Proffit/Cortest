<?php

namespace App\Controller;

use App\Core\Recherche\ParametreRechercheStorage;
use App\Core\ReponseCandidat\ReponsesCandidatSessionStorage;
use App\Core\ReponseCandidat\ReponsesCandidatStorage;
use App\Entity\ReponseCandidat;
use App\Form\Data\ParametresRecherche;
use App\Form\Data\ReponseCandidatChecked;
use App\Form\RechercheParametersType;
use App\Form\ReponsesCandidatCheckedType;
use App\Repository\ReponseCandidatRepository;
use DateTime;
use LogicException;
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
     * TODO ne pas passer par sessionStorage, mais par storage directement (encapsulation correcte)
     * @param ReponsesCandidatSessionStorage $reponsesCandidatSessionStorage
     * @param int $reponse_id
     * @return RedirectResponse
     */
    #[Route("/deselectionner/{reponse_id}", "deselectionner")]
    public function removeReponseCandidat(int $reponse_id): RedirectResponse
    {
        $cached_reponses = $this->reponsesCandidatSessionStorage->getOrSetDefault([]);
        $this->reponsesCandidatSessionStorage->set(array_diff($cached_reponses, array($reponse_id)));
        $this->addFlash("success", "Le candidat a été retiré.");
        return $this->redirectToRoute("recherche_index");
    }

    #[Route("/selectionner", name: "selectionner")]
    public function selectionner(
        ParametreRechercheStorage $parametreRechercheStorage,
        Request                   $request
    ): RedirectResponse
    {
        $parametresRecherche = $parametreRechercheStorage->get();

        if ($parametresRecherche == null) {
            throw new LogicException("TODO");
        }

        $sessionCheckedReponsesIds = $this->getSessionCheckedReponsesIds();
        $checkedReponses = $this->getDisplayableCheckedReponses($parametresRecherche, $sessionCheckedReponsesIds);
        $form = $this->formReponsesCandidats($checkedReponses);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($checkedReponses as $id => $checked) {
                if ($checked && !in_array($id, $sessionCheckedReponsesIds)) {
                    $sessionCheckedReponsesIds[] = $id;
                }
            }

            $this->reponsesCandidatSessionStorage->set($sessionCheckedReponsesIds);
        }

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
        $parametresRecherche = $parametreRechercheStorage->getOrSetDefault(new ParametresRecherche(
            filtrePrenom: "",
            filtreNom: "",
            page: 1,
            filtreDateDeNaissanceMin: new DateTime(self::LOWEST_TIME),
            filtreDateDeNaissanceMax: new DateTime("now"),
            dateSession: null,
            niveauScolaire: null,
            session: null
        ));

        $pageCount = $this->pageCount();
        $form = $this->formParametres($parametresRecherche, $pageCount);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            for ($page = 0; $page < $pageCount; $page++) {

                /** @var ClickableInterface $submitPage */
                $submitPage = $form->get(RechercheParametersType::SUBMIT_PAGE_PREFIX_KEY . $page);

                if ($submitPage->isClicked()) {
                    $logger->debug("Choix de la page " . $page);
                    $parametresRecherche->page = $page;
                }
            }

            $parametreRechercheStorage->set($parametresRecherche);
        }

        $selectionnesIds = $this->reponsesCandidatSessionStorage->getOrSetDefault([]);
        $selectionnes = $reponseCandidatRepository->findAllByIds($selectionnesIds);

        $sessionCheckedReponsesIds = $this->getSessionCheckedReponsesIds();
        $checkedReponses = $this->getDisplayableCheckedReponses(
            parametresRecherche: $parametresRecherche,
            sessionCheckedReponsesIds: $sessionCheckedReponsesIds
        );
        $formReponsesCandidats = $this->formReponsesCandidats($checkedReponses);

        return $this->renderIndexForm(
            formParametres: $form,
            formReponsesCandidats: $formReponsesCandidats,
            selectionnes: $selectionnes
        );
    }

    /**
     * @param FormInterface $formParametres
     * @param FormInterface $formReponsesCandidats
     * @param ReponseCandidat[] $selectionnes
     * @return Response
     */
    private function renderIndexForm(
        FormInterface $formParametres,
        FormInterface $formReponsesCandidats,
        array         $selectionnes
    ): Response
    {
        return $this->render("recherche/index.html.twig", [
            "formReponsesCandidat" => $formReponsesCandidats->createView(),
            "formParametres" => $formParametres->createView(),
            "selectionnes" => $selectionnes
        ]);
    }

    private function pageCount(): int
    {
        $reponsesCount = $this->reponseCandidatRepository->count([]);
        return (int)ceil($reponsesCount / ParametresRecherche::PAGE_SIZE);
    }

    private function getSessionCheckedReponsesIds(): array
    {
        return $this->reponsesCandidatSessionStorage->getOrSetDefault([]);
    }

    /**
     * @param ParametresRecherche $parametresRecherche
     * @param int[] $sessionCheckedReponsesIds
     * @return ReponseCandidatChecked[]
     */
    private function getDisplayableCheckedReponses(
        ParametresRecherche $parametresRecherche,
        array               $sessionCheckedReponsesIds
    ): array
    {
        $reponsesCandidat = $this->reponseCandidatRepository->findAllFromParameters($parametresRecherche);

        $checkedReponses = [];
        foreach ($reponsesCandidat as $reponseCandidat) {
            $checked = in_array($reponseCandidat->id, $sessionCheckedReponsesIds);
            $checkedReponses[$reponseCandidat->id] = new ReponseCandidatChecked($reponseCandidat, $checked);
        }

        return $checkedReponses;
    }

    /**
     * @param ReponseCandidatChecked[] $checkedReponses
     * @return FormInterface
     */
    private function formReponsesCandidats(
        array $checkedReponses
    ): FormInterface
    {
        return $this->createForm(ReponsesCandidatCheckedType::class, $checkedReponses);
    }

    private function formParametres(
        ParametresRecherche $parametres,
        int                 $pageCount
    ): FormInterface
    {
        return $this->createForm(RechercheParametersType::class, $parametres, [RechercheParametersType::OPTION_PAGE_COUNT_KEY => $pageCount]);
    }
}