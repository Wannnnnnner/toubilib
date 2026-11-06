<?php
namespace toubilib\domain\entities;

class Specialite
{
    public function __construct(
        public readonly int $id,
        public readonly string $libelle,
        public readonly ?string $description
    ) {
    }
}