<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

use DateTimeImmutable;
use toubilib\domain\exceptions\AnnulationRendezVousImpossible;

class RendezVous
{
    public function __construct(
        private readonly string $id,
        private readonly DateTimeImmutable $dateHeureDebut,
        private RendezVousStatus $status = RendezVousStatus::PLANIFIE,
    ) {
    }

    public function annuler(?DateTimeImmutable $maintenant = null): void
    {
        $maintenant ??= new DateTimeImmutable();

        if ($this->status !== RendezVousStatus::PLANIFIE) {
            throw new AnnulationRendezVousImpossible('Le rendez-vous ne peut plus être annulé.');
        }

        if ($this->dateHeureDebut <= $maintenant) {
            throw new AnnulationRendezVousImpossible(
                'Un rendez-vous passé ou en cours ne peut pas être annulé.'
            );
        }

        $this->status = RendezVousStatus::ANNULE;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateHeureDebut(): DateTimeImmutable
    {
        return $this->dateHeureDebut;
    }

    public function getStatus(): RendezVousStatus
    {
        return $this->status;
    }
}