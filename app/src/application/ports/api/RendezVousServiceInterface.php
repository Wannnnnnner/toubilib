<?php

declare(strict_types=1);

namespace toubilib\application\ports\api;

use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\dtos\RendezVousDTO;
use toubilib\domain\entities\RendezVous;

interface RendezVousServiceInterface
{
    public function annulerRdv(string $rendezVousId): RendezVousDTO;

    // PS: Changez la sortie pour un DTO
    public function createRdv(CreateRdvDTO $rendezVous): RendezVousDTO;
}