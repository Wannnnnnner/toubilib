<?php

declare(strict_types=1);

namespace toubilib\application\dtos;

class PraticienDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $titre,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $ville,
        public readonly string $email,
        public readonly string $telephone,
        public readonly bool $accepteNouveauPatient
    ) {
    }
}