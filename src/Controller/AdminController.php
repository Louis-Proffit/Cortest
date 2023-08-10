<?php

namespace App\Controller;

use App\Core\Activite\LogEntryProcessor;
use App\Entity\CortestUser;
use App\Form\CreerCortestUserType;
use App\Form\Generic\CortestUserType;
use App\Form\MotDePasseCortestUserType;
use App\Repository\CortestUserRepository;
use App\Repository\LogEntryRepository;
use App\Security\CheckAdministrateurCount;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Opérations accessibles pour un administrateur.
 */
#[Route("/admin", name: "admin_")]
class AdminController extends AbstractController
{

    /**
     * Page d'accueil de l'administrateur
     * @return Response
     */
    #[Route("/index", name: "index")]
    public function index(): Response
    {
        return $this->render("admin/index.html.twig");
    }

    /**
     * Gestion des utilisateurs
     * @param CortestUserRepository $userRepository
     * @return Response
     */
    #[Route("/utilisateurs", name: "utilisateurs")]
    public function utilisateurs(
        CortestUserRepository $userRepository,
    ): Response
    {
        $items = $userRepository->findBy([], orderBy: ["id" => Criteria::DESC]);

        return $this->render("admin/index_utilisateur.html.twig", ["users" => $items]);
    }

    /**
     * Formulaire pour la création d'un utilisateur
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        LoggerInterface             $logger,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request                     $request
    ): RedirectResponse|Response
    {
        $user = new CortestUser(id: 0, username: "", password: "", role: CortestUser::ROLE_CORRECTEUR);

        $form = $this->createForm(CreerCortestUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $user->password = $userPasswordHasher->hashPassword(
                $user,
                $user->password
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $logger->info("Création d'un utilisateur : id=" . $user->id);
            $this->addFlash("info", "Création enregistrée");

            return $this->redirectToRoute("admin_index");

        }

        return $this->render("admin/creer.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire lour la modification du mot de passe d'un utilisateur
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Request $request
     * @param CortestUser $user
     * @return Response
     */
    #[Route("/modifier-mdp/{id}", name: "modifier_mdp")]
    public function modifierMotDePasse(
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request                     $request,
        CortestUser                 $user // Mapped from $id
    ): Response
    {
        $form = $this->createForm(MotDePasseCortestUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $user->password = $userPasswordHasher->hashPassword(
                $user,
                $user->password
            );

            $entityManager->flush();

            return $this->redirectToRoute("admin_index");
        }

        return $this->render("admin/modifier_mdp.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire pour modifier un utilisateur, à l'exception du mot de passe
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CheckAdministrateurCount $checkAdministrateurCount
     * @param CortestUser $user
     * @return Response
     * @see CortestUserType
     */
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        EntityManagerInterface   $entityManager,
        Request                  $request,
        CheckAdministrateurCount $checkAdministrateurCount,
        CortestUser              $user // Mapped by id
    ): Response
    {

        $form = $this->createForm(CortestUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            if ($user->role !== CortestUser::ROLE_ADMINISTRATEUR) {

                if ($checkAdministrateurCount->atLeastOneAdministrateur()) {
                    $entityManager->flush();

                    return $this->redirectToRoute("admin_utilisateurs");
                } else {
                    $this->addFlash("danger", "Opération impossible, il ne resterait plus d'administrateur");
                }
            }
        }

        return $this->render("admin/modifier.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Supprimer un utilisateur.
     * Vérifie que l'utilisateur supprimé n'est pas l'actuel, ou que si c'est l'actuel, il reste au moins un autre administrateur.
     * @param EntityManagerInterface $entityManager
     * @param CheckAdministrateurCount $checkAdministrateurCount
     * @param CortestUser $currentUser
     * @param CortestUser $user
     * @return RedirectResponse
     */
    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        EntityManagerInterface     $entityManager,
        CheckAdministrateurCount   $checkAdministrateurCount,
        #[CurrentUser] CortestUser $currentUser,
        CortestUser                $user): RedirectResponse
    {

        if ($currentUser->id !== $user->id || $checkAdministrateurCount->atLeastTwoAdministrateurs()) {

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash("info", "Suppression enregistrée");
        } else {
            $this->addFlash("warning", "Impossible de supprimer le dernier administrateur.");
        }

        return $this->redirectToRoute("admin_index");
    }

    #[Route("/activite/{page}", name: "activite", defaults: ["page" => 1])]
    public function activite(
        LogEntryRepository $logEntryRepository,
        LogEntryProcessor  $logEntryProcessor,
        int                $page
    ): Response
    {
        if ($page <= 0) {
            return $this->redirectToRoute("admin_activite", ["page" => 1]);
        }

        $count = $logEntryRepository->count([]);

        $pages = ceil($count / LogEntryRepository::PAGE_SIZE);

        if ($page > $pages) {
            return $this->redirectToRoute("admin_activite", ["page" => $pages]);
        }
        $logEntries = $logEntryRepository->findAllAtPage($page);
        $wrappedLogEntries = $logEntryProcessor->processAll(logEntries: $logEntries);
        return $this->render("admin/index_activite.html.twig", [
            "logs" => $wrappedLogEntries,
            "pages" => $pages,
            "page" => $page,
            "action_names" => LogEntryProcessor::ACTION_NAMES,
            "action_infos" => LogEntryProcessor::ACTION_INFOS,
            "class_names" => LogEntryProcessor::CLASS_NAMES,
            "class_infos" => LogEntryProcessor::CLASS_INFOS
        ]);
    }
}