<?php

namespace App\Controller;

use App\Entity\CortestUser;
use App\Form\CortestUserType;
use App\Form\CreerCortestUserType;
use App\Form\MotDePasseCortestUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin", name: "admin_")]
class AdminController extends AbstractController
{

    #[Route("/index", name: "index")]
    public function index(
        UserRepository $user_repository,
    ): Response
    {
        $items = $user_repository->findAll();

        return $this->render("admin/index.html.twig", ["users" => $items]);
    }

    #[Route("/creer", name: "creer")]
    public function creer(
        EntityManagerInterface      $entity_manager,
        UserPasswordHasherInterface $user_password_hasher,
        Request                     $request
    ): RedirectResponse|Response
    {
        $user = new CortestUser(
            id: 0, username: "", password: "", role: CortestUser::ROLE_CORRECTEUR
        );

        $form = $this->createForm(CreerCortestUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {

            $user->password = $user_password_hasher->hashPassword(
                $user,
                $user->password
            );

            $entity_manager->persist($user);
            $entity_manager->flush();
            $this->addFlash("info", "Création enregistrée");

            return $this->redirectToRoute("admin_index");

        }

        return $this->render("admin/creer.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier-mdp/{id}", name: "modifier_mdp")]
    public function modifierMotDePasse(
        UserRepository              $user_repository,
        EntityManagerInterface      $entity_manager,
        UserPasswordHasherInterface $user_password_hasher,
        Request                     $request,
        int                         $id
    ): Response
    {
        $item = $user_repository->find($id);

        $form = $this->createForm(MotDePasseCortestUserType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $item->password = $user_password_hasher->hashPassword(
                $item,
                $item->password
            );

            $entity_manager->flush();

            return $this->redirectToRoute("admin_index");
        }

        return $this->render("admin/modifier_mdp.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/modifier/{id}", name: "modifier")]
    public function modifier(
        UserRepository         $user_repository,
        EntityManagerInterface $entity_manager,
        Request                $request,
        int                    $id
    ): Response
    {
        $item = $user_repository->find($id);

        $form = $this->createForm(CortestUserType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            if ($item->role !== CortestUser::ROLE_ADMINISTRATEUR) {

                $administrateurs = $user_repository->findBy(["role" => CortestUser::ROLE_ADMINISTRATEUR]);

                if (count($administrateurs) >= 1) {

                    $entity_manager->flush();

                    return $this->redirectToRoute("admin_index");
                }
            }
        }

        return $this->render("admin/modifier.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/supprimer/{id}", name: "supprimer")]
    public function supprimer(
        UserRepository         $user_repository,
        EntityManagerInterface $entity_manager,
        int                    $id): RedirectResponse
    {
        $item = $user_repository->find($id);
        // TODO don't remove last

        $entity_manager->remove($item);
        $entity_manager->flush();

        $this->addFlash("info", "Suppression enregistrée");

        return $this->redirectToRoute("admin_index");
    }
}