<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

use DateTimeImmutable;
use InvalidArgumentException;
use toubilib\domain\exceptions\AnnulationRendezVousImpossible;

class RendezVous
{
    private readonly DateTimeImmutable $dateHeureFin;
    private readonly int $duree;

    public function __construct(
        private readonly string $id,
        private readonly string $praticienId,
        private readonly string $patientId,
        private readonly DateTimeImmutable $dateHeureDebut,
        private readonly string $motifVisite,
        private readonly DateTimeImmutable $dateCreation = new DateTimeImmutable(),
        private RendezVousStatus $status = RendezVousStatus::PLANIFIE,
    ) {
        $this->duree = match ($motifVisite) {
            'CI' => 30,
            'C0' => 20,
            'CS' => 15,
            default => throw new InvalidArgumentException("Motif de visite invalide : $motifVisite"),
        };
        $this->dateHeureFin = $dateHeureDebut->modify('+' . $this->duree . ' minutes');
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

    public function chevauche(RendezVous $r): bool
    {
        return $this->dateHeureDebut < $r->dateHeureFin
            && $this->dateHeureFin > $r->dateHeureDebut;
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
