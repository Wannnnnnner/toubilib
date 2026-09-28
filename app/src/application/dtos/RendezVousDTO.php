<?php

declare(strict_types=1);

namespace toubilib\application\dtos;

use DateTimeImmutable;
use toubilib\domain\entities\RendezVous;

class RendezVousDTO
{
    public readonly string $id;
    public readonly string $praticienId;
    public readonly string $patientId;
    public readonly DateTimeImmutable $dateHeureDebut;
    public readonly DateTimeImmutable $dateHeureFin;
    public readonly int $duree;
    public readonly string $motifVisite;
    public readonly string $status;

    public function __construct(RendezVous $rdv)
    {
        $this->id = $rdv->getId();
        $this->praticienId = $rdv->getPraticienId();
        $this->patientId = $rdv->getPatientId();
        $this->dateHeureDebut = $rdv->getDateHeureDebut();
        $this->dateHeureFin = $rdv->getDateHeureFin();
        $this->duree = $rdv->getDuree();
        $this->motifVisite = $rdv->getMotifVisite();
        $this->status = $rdv->getStatus()->name;
    }

    public static function fromEntity(RendezVous $rdv): self
    {
        return new self($rdv);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'praticien_id' => $this->praticienId,
            'patient_id' => $this->patientId,
            'date_heure_debut' => $this->dateHeureDebut->format('Y-m-d H:i:s'),
            'date_heure_fin' => $this->dateHeureFin->format('Y-m-d H:i:s'),
            'duree' => $this->duree,
            'motif_visite' => $this->motifVisite,
            'status' => $this->status,
        ];
    }
}