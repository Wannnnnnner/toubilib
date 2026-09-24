<?php

declare(strict_types=1);

namespace toubilib\application\ports\spi;

use toubilib\domain\entities\Praticien;

interface PraticienRepositoryInterface
{
    public function findById(string $praticien): ?Praticien;

    public function save(Praticien $praticien): void;

    public function update(Praticien $praticien): void;
}