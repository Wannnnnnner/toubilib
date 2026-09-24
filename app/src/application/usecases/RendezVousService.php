<?php

declare(strict_types=1);

namespace toubilib\application\usecases;

use RuntimeException;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;
use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\validator\CreateRdvValidator;
use toubilib\domain\entities\RendezVousStatus;

final class RendezVousService implements RendezVousServiceInterface
{
    public function __construct(
        private CreateRdvValidator $validator,
        private readonly RendezVousRepositoryInterface $repository,

    ) {
    }

    public function annulerRdv(string $rendezVousId): RendezVous
    {
        $rendezVous = $this->repository->findById($rendezVousId);

        if ($rendezVous === null) {
            throw new RuntimeException('Rendez-vous introuvable.');
        }

        $rendezVous->annuler();
        $this->repository->update($rendezVous);

        return $rendezVous;
    }

    public function createRdv(CreateRdvDTO $rendezVous): RendezVous
    {
        try {
            $rendezVousValider = $this->validator->validate($rendezVous);
        } catch (ValidationException $e) {
            throw new \DomainException("Owner can not be found.");
        }
        $rendezVous = new RendezVous(
            id: Uuid::uuid4()->toString(),
            dateHeureDebut: $rendezVous->DateHeure,
            status: RendezVousStatus::PLANIFIE
        );
        $this->RendezVousRepository->save($rendezVous);
        return $rendezVous;
    }
}