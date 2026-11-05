<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

use toubilib\domain\entities\RendezVous;

class Praticien
{

    public const JOUR_DE_TRAVAIL = [1,2,3,4,5];

    
    private string $id;
    private string $nom;
    private string $prenom;
    private string $ville;
    private string $email;
    private string $telephone;
    private string $rppsId;
    private string $titre;
    private bool $accepteNouveauPatient;

    /** @var list<RendezVous> */
    private array $rdvs = [];

    public function __construct(
        string $id,
        string $nom,
        string $prenom,
        string $ville,
        string $email,
        string $telephone,
        string $rppsId,
        string $titre,
        bool $accepteNouveauPatient
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->ville = $ville;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->rppsId = $rppsId;
        $this->titre = $titre;
        $this->accepteNouveauPatient = $accepteNouveauPatient;
    }

public function estDisponible(RendezVous $rdv): bool
    {
        foreach ($this->rdvs as $r) {
            if ($r->chevauche($rdv)) {
                return false;
            }
        }

        return true;
    }

    public function ajouterRendezVous(RendezVous $rdv): void
    {
        $this->rdvs[] = $rdv;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getRppsId(): string
    {
        return $this->rppsId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function accepteNouveauPatient(): bool
    {
        return $this->accepteNouveauPatient;
    }
}
