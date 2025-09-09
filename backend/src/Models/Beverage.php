<?php

namespace App\Models;

use DateTimeImmutable;

class Beverage
{
    private ?int $id = null;
    private string $name;
    private string $capacity;
    private string $brand;
    private TypeBeverage $type;
    private DateTimeImmutable $createdAt;
    private string $createdBy;
    private ?string $alteredBy = null;
    private ?DateTimeImmutable $alteredAt = null;

    public function __construct(string $name, string $capacity, TypeBeverage $type, string $createdBy)
    {
        $this->name = $name;
        $this->capacity = $capacity;
        $this->type = $type;
        $this->createdBy = $createdBy;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getType(): TypeBeverage
    {
        return $this->type;
    }

    public function getCapacity(): string
    {
        return $this->capacity;
    }

    public function setAlteradoPor(string $alteradoPor): void
    {
        $this->alteredBy = $alteradoPor;
        $this->alteredAt = new DateTimeImmutable();
    }
}