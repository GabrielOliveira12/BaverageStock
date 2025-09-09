<?php

namespace App\Models;

class TypeBeverage
{  
   public ?int $id;
   public string $name;
   public float $capacity;

   public function __construct(?int $id, string $name, float $capacity)
   {
       $this->id = $id;
       $this->name = $name;
       $this->capacity = $capacity;
   }

   public function getId(): ?int
   {
       return $this->id;
   }

   public function getName(): string
   {
       return $this->name;
   }

   public function getCapacity(): float
   {
       return $this->capacity;
   }
}