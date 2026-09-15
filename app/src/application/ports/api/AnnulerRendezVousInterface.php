<?php

declare(strict_types=1);

namespace toubilib\application\ports\api;

use toubilib\domain\entities\RendezVous;

interface AnnulerRendezVousInterface
{
    public function execute(string $rendezVousId): RendezVous;
}