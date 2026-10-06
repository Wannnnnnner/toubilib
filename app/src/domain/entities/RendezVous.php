<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

use DateTimeImmutable;
use toubilib\domain\exceptions\AnnulationRendezVousImpossible;

class RendezVous
{
    private readonly DateTimeImmutable $dateHeureFin;

    public function __construct(
        private readonly string $id,
        private readonly string $praticienId,
        private readonly string $patientId,
        private readonly DateTimeImmutable $dateHeureDebut,
        private readonly string $motifVisite,
        private readonly int $duree = 30,
        private readonly DateTimeImmutable $dateCreation = new DateTimeImmutable(),
        private RendezVousStatus $status = RendezVousStatus::PLANIFIE,
    ) {
        $this->dateHeureFin = $dateHeureDebut->modify('+' . $duree . ' minutes');
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

    public function getPraticienId(): string
    {
        return $this->praticienId;
    }

    public function getPatientId(): string
    {
        return $this->patientId;
    }

    public function getDateHeureDebut(): DateTimeImmutable
    {
        return $this->dateHeureDebut;
    }

    public function getDateHeureFin(): DateTimeImmutable
    {
        return $this->dateHeureFin;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function getMotifVisite(): string
    {
        return $this->motifVisite;
    }

    public function getDateCreation(): DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getStatus(): RendezVousStatus
    {
        return $this->status;
    }
}