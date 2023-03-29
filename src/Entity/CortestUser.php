<?php

namespace App\Entity;

use App\Constraint\CortestPassword;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class CortestUser implements UserInterface, PasswordAuthenticatedUserInterface
{

    const ROLE_ADMINISTRATEUR = "ROLE_ADMINISTRATEUR";
    const ROLE_PSYCOLOGUE = "ROLE_PSYCHOLOGUE";
    const ROLE_CORRECTEUR = "ROLE_CORRECTEUR";
    const ROLES = [
        self::ROLE_ADMINISTRATEUR, self::ROLE_PSYCOLOGUE, self::ROLE_CORRECTEUR
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[NotBlank]
    #[Length(max: 100 )]
    #[ORM\Column(length: 100, unique: true)]
    public string $username;

    #[CortestPassword(min:5)]
    #[ORM\Column]
    public string $password;

    #[Choice(choices: self::ROLES)]
    #[ORM\Column]
    public string $role;

    /**
     * @param int $id
     * @param string $username
     * @param string $password
     * @param string $role
     */
    public function __construct(int $id, string $username, string $password, string $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


}
