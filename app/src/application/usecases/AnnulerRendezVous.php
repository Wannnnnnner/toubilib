<?php

declare(strict_types=1);

namespace toubilib\application\usecases;

use RuntimeException;
use toubilib\application\ports\api\AnnulerRendezVousInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;

final class AnnulerRendezVous implements AnnulerRendezVousInterface
{
    public function __construct(
        private readonly RendezVousRepositoryInterface $repository,
    ) {
    }

    public function execute(string $rendezVousId): RendezVous
    {
        $rendezVous = $this->repository->findById($rendezVousId);

        if ($rendezVous === null) {
            throw new RuntimeException('Rendez-vous introuvable.');
        }

        $rendezVous->annuler();
        $this->repository->save($rendezVous);

        return $rendezVous;
    }
}