<?php

declare(strict_types=1);

namespace toubilib\application\usecases;

use Ramsey\Uuid\Uuid;
use RuntimeException;
use toubilib\application\dtos\RendezVousDTO;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;
use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\validator\CreateRdvValidator;
use toubilib\domain\entities\RendezVousStatus;
use toubilib\application\exceptions\ValidationException;

final class RendezVousService implements RendezVousServiceInterface
{
    public function __construct(
        private CreateRdvValidator $validator,
        private readonly RendezVousRepositoryInterface $repository,
    ) {
    }

    public function annulerRdv(string $rendezVousId): RendezVousDTO
    {
        $rendezVous = $this->repository->findById($rendezVousId);

        if ($rendezVous === null) {
            throw new RuntimeException('Rendez-vous introuvable.');
        }

        $rendezVous->annuler();
        $this->repository->update($rendezVous);

        return RendezVousDTO::fromEntity($rendezVous);
    }

    public function createRdv(CreateRdvDTO $rendezVous): RendezVousDTO
    {
        try {
            $rendezVousValider = $this->validator->validate($rendezVous);
        } catch (ValidationException $e) {
            throw new \DomainException("Owner can not be found.");
        }
        $rdv = new RendezVous(
            id: Uuid::uuid4()->toString(),
            praticienId: $rendezVous->idMedecin,
            patientId: $rendezVous->idPatient,
            dateHeureDebut: $rendezVous->dateHeure,
            motifVisite: $rendezVous->motif,
            status: RendezVousStatus::PLANIFIE
        );
        $this->repository->save($rdv);
        return RendezVousDTO::fromEntity($rdv);
    }
}