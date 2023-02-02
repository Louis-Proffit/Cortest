<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class SensReserveAutre
{

    #[Id]
    #[GeneratedValue]
    #[Column]
    public int $id;

    #[Column]
    public int $type_concours;

    #[Column]
    public bool $reserve_utile;

    #[Column]
    public bool $autre_1_utile;

    #[Column]
    public bool $autre_2_util;

    /**
     * @param int $id
     * @param int $type_concours
     * @param bool $reserve_utile
     * @param bool $autre_1_utile
     * @param bool $autre_2_util
     */
    public function __construct(int $id, int $type_concours, bool $reserve_utile, bool $autre_1_utile, bool $autre_2_util)
    {
        $this->id = $id;
        $this->type_concours = $type_concours;
        $this->reserve_utile = $reserve_utile;
        $this->autre_1_utile = $autre_1_utile;
        $this->autre_2_util = $autre_2_util;
    }


}