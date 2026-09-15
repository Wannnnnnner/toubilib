<?php

declare(strict_types=1);

namespace toubilib\application\ports\spi;

use toubilib\domain\entities\RendezVous;

interface RendezVousRepositoryInterface
{
    public function findById(string $rendezVousId): ?RendezVous;

    public function save(RendezVous $rendezVous): void;
}