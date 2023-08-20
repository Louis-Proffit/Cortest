<?php

namespace App\Controller;

use App\Core\Activite\ActiviteLogger;
use App\Core\Activite\CortestLogEntryProcessor;
use App\Entity\CortestLogEntry;
use App\Entity\CortestUser;
use App\Form\CreerCortestUserType;
use App\Form\Generic\CortestUserType;
use App\Form\MotDePasseCortestUserType;
use App\Repository\CortestLogEntryRepository;
use App\Repository\CortestUserRepository;
use App\Security\CheckAdministrateurCount;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
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
        $users = $userRepository->findBy([], orderBy: ["id" => Criteria::DESC]);

        return $this->render("admin/index_utilisateur.html.twig", ["users" => $users]);
    }

    /**
     * Formulaire pour la création d'un utilisateur
     * @param ActiviteLogger $activiteLogger
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Request $request
     * @return Response
     */
    #[Route("/creer", name: "creer")]
    public function creer(
        ActiviteLogger              $activiteLogger,
        LoggerInterface             $logger,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request                     $request
    ): Response
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
            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_CREER,
                object: $user,
                message: "Création d'un utilisateur par formulaire"
            );
            $entityManager->flush();


            $logger->info("Création d'un utilisateur", ["user" => $user]);
            $this->addFlash("info", "Utilisateur enregistré");

            return $this->redirectToRoute("admin_index");

        }

        return $this->render("admin/creer.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire lour la modification du mot de passe d'un utilisateur
     * @param ActiviteLogger $activiteLogger
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Request $request
     * @param CortestUser $user
     * @return Response
     */
    #[Route("/modifier-mdp/{id}", name: "modifier_mdp")]
    public function modifierMotDePasse(
        ActiviteLogger              $activiteLogger,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request                     $request,
        CortestUser                 $user
    ): Response
    {
        $form = $this->createForm(MotDePasseCortestUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $user->password = $userPasswordHasher->hashPassword(
                $user,
                $user->password
            );

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_MODIFIER,
                object: $user,
                message: "Modification du mot de passe"
            );

            $entityManager->flush();

            return $this->redirectToRoute("admin_index");
        }

        return $this->render("admin/modifier_mdp.html.twig", ["form" => $form->createView()]);
    }

    /**
     * Formulaire pour modifier un utilisateur, à l'exception du mot de passe
     * @param ActiviteLogger $activiteLogger
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CheckAdministrateurCount $checkAdministrateurCount
     * @param CortestUser $user
     * @return Response
     * @see CortestUserType
     */
    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        ActiviteLogger           $activiteLogger,
        EntityManagerInterface   $entityManager,
        Request                  $request,
        CheckAdministrateurCount $checkAdministrateurCount,
        CortestUser              $user
    ): Response
    {

        $form = $this->createForm(CortestUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            if ($user->role !== CortestUser::ROLE_ADMINISTRATEUR) {

                if ($checkAdministrateurCount->atLeastOneAdministrateur()) {

                    $activiteLogger->persistAction(
                        action: CortestLogEntry::ACTION_MODIFIER,
                        object: $user,
                        message: "Modification de l'utilisateur"
                    );
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
     * @param ActiviteLogger $activiteLogger
     * @param EntityManagerInterface $entityManager
     * @param CheckAdministrateurCount $checkAdministrateurCount
     * @param CortestUser $currentUser
     * @param CortestUser $user
     * @return RedirectResponse
     */
    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        ActiviteLogger             $activiteLogger,
        EntityManagerInterface     $entityManager,
        CheckAdministrateurCount   $checkAdministrateurCount,
        #[CurrentUser] CortestUser $currentUser,
        CortestUser                $user): RedirectResponse
    {

        if ($currentUser->id !== $user->id || $checkAdministrateurCount->atLeastTwoAdministrateurs()) {

            $activiteLogger->persistAction(
                action: CortestLogEntry::ACTION_SUPPRIMER,
                object: $user,
                message: "Suppression de l'utilisateur"
            );
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
        CortestLogEntryRepository $logEntryRepository,
        CortestLogEntryProcessor  $logEntryProcessor,
        int                       $page
    ): Response
    {
        if ($page <= 0) {
            return $this->redirectToRoute("admin_activite", ["page" => 1]);
        }

        $count = $logEntryRepository->count([]);

        $pages = ceil($count / CortestLogEntryRepository::PAGE_SIZE);

        if ($pages == 0) {
            $pages = 1;
        }

        if ($page > $pages) {
            return $this->redirectToRoute("admin_activite", ["page" => $pages]);
        }
        $logEntries = $logEntryRepository->findAllAtPage($page);
        $wrappedLogEntries = $logEntryProcessor->processAll(logEntries: $logEntries);

        return $this->render("admin/index_activite.html.twig", [
            "logs" => $wrappedLogEntries,
            "pages" => $pages,
            "page" => $page,
            "action_names" => CortestLogEntryProcessor::ACTION_NAMES,
            "action_infos" => CortestLogEntryProcessor::ACTION_INFOS,
            "class_names" => CortestLogEntryProcessor::CLASS_NAMES,
            "class_infos" => CortestLogEntryProcessor::CLASS_INFOS
        ]);
    }
}