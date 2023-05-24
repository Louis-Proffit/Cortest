<?php

namespace App\Constraint;

use Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueDTO extends Constraint
{
    public ?string $atPath = null;
    public string $entityClass;
    public string $field;
    public string $message = 'error.duplicate';
    public ServiceEntityRepository $repository;

    public function getTargets(): string
    {
        return parent::CLASS_CONSTRAINT;
    }

    public function __construct(mixed $field, mixed $message, mixed $repository, mixed $options = null, array $groups = null, mixed $payload = null)
    {
        $this->field = $field;
        $this->message = $message;
        $this->repository = $repository;
        parent::__construct($options, $groups, $payload);
    }
}