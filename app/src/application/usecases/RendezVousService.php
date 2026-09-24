<?php

declare(strict_types=1);

namespace toubilib\application\usecases;

use RuntimeException;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVous;
use toubilib\application\dtos\CreateRdvDTO;

final class RendezVousService implements RendezVousServiceInterface
{
    public function __construct(
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
            throw new \DomainException("Owner {$rendezVous->ownerId} can not be found.");
        }
        $userStory = new UserStory(
            id: Uuid::uuid4()->toString(),
            title: $rendezVous->title,
            description: $rendezVous->description,
            status: UserStoryStatus::TODO
        );
        $this->userStoryRepository->save($userStory);
        return UserStoryOutputDTO::fromEntity($userStory);
    }
}