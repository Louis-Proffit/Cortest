<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity]
class CortestLogEntry
{
    const ACTION_CREER = "creer";
    const ACTION_MODIFIER = "modifier";
    const ACTION_CALCULER = "calculer";
    const ACTION_SUPPRIMER = "supprimer";
    const ACTION_EXPORTER = "exporter";
    const ACTION_UTILISATEUR = "utilisateur";

    const ACTIONS = [
        self::ACTION_CREER,
        self::ACTION_MODIFIER,
        self::ACTION_CALCULER,
        self::ACTION_SUPPRIMER,
        self::ACTION_EXPORTER,
        self::ACTION_UTILISATEUR
    ];

    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    public int $id;

    #[Choice(options: self::ACTIONS)]
    #[ORM\Column(type: Types::STRING, length: 20)]
    public string $action;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public DateTime $logged_at;

    #[ORM\Column(nullable: true)]
    public int|null $object_id;

    #[ORM\Column(type: Types::STRING, length: 256, nullable: true)]
    public string|null $object_class;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public array|null $data;

    #[ORM\Column(length: 256)]
    public string $username;

    #[ORM\Column(length: 2048)]
    public string $message;

    /**
     * @param int $id
     * @param string $action
     * @param DateTime $logged_at
     * @param int|null $object_id
     * @param string|null $object_class
     * @param array|null $data
     * @param string $username
     * @param string $message
     */
    public function __construct(int $id, string $action, DateTime $logged_at, ?int $object_id, ?string $object_class, ?array $data, string $username, string $message)
    {
        $this->id = $id;
        $this->action = $action;
        $this->logged_at = $logged_at;
        $this->object_id = $object_id;
        $this->object_class = $object_class;
        $this->data = $data;
        $this->username = $username;
        $this->message = $message;
    }
}
