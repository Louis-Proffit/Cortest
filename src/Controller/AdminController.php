<?php

namespace App\Controller;

use App\Entity\CortestUser;
use App\Form\CortestUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin", name: "admin_")]
class AdminController extends AbstractCrudController
{

    public function __construct(
        UserRepository $repository,
    )
    {
        parent::__construct(repository: $repository, formClass: CortestUserType::class, indexRoute: "admin_index");
    }

    protected function produce(): CortestUser
    {
        return new CortestUser(
            id: 0, username: "", password: "", role: CortestUser::ROLE_CORRECTEUR
        );
    }

    protected function renderIndex(array $items): Response
    {
        return $this->render("crud/index_user.html.twig", ["users" => $items]);
    }

    /**
     * @param EntityManagerInterface $entity_manager
     * @param FormInterface $form
     * @param CortestUser $item
     * @return bool
     */
    protected function postValidate(EntityManagerInterface $entity_manager, FormInterface $form, $item): bool
    {
        if ($item->role !== CortestUser::ROLE_ADMINISTRATEUR) {
            /** @var CortestUser[] $administrateurs */
            $administrateurs = $this->repository->findBy(["role" => CortestUser::ROLE_ADMINISTRATEUR]);

            if (count($administrateurs) <= 1) {

                $form->get("role")->addError(new FormError("Impossible de supprimer le dernier administrateur"));
                return false;
            }
        }
        return true;
    }
}