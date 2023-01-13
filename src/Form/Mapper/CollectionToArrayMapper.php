<?php

namespace App\Form\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

class CollectionToArrayMapper implements DataTransformerInterface
{

    /**
     * @param Collection $value
     * @return array
     */
    public function transform(mixed $value): array
    {
        return $value->toArray();
    }

    /**
     * @param array $value
     * @return ArrayCollection
     */
    public function reverseTransform(mixed $value): ArrayCollection
    {
        return new ArrayCollection($value);
    }
}