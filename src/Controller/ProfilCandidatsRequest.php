<?php

namespace App\Controller;

class ProfilCandidatsRequest
{
    private int $batterie_id;
    private array $ids;

    /**
     * @param int $batterie_id
     * @param array $ids
     */
    public function __construct(int $batterie_id, array $ids)
    {
        $this->batterie_id = $batterie_id;
        $this->ids = $ids;
    }

    /**
     * @return int
     */
    public function getBatterieId(): int
    {
        return $this->batterie_id;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }


}