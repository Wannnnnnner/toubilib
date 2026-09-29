<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

use DateTimeImmutable;

class Patient
{
    private string $id;
    private string $nom;
    private string $prenom;
    private DateTimeImmutable $dateNaissance;
    private string $adresse;
    private string $codePostal;
    private string $ville;
    private string $email;
    private string $telephone;

    public function __construct(
        string $id,
        string $nom,
        string $prenom,
        DateTimeImmutable $dateNaissance,
        string $adresse,
        string $ville,
        string $codePostal,
        string $telephone,
        string $email
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
        $this->telephone = $telephone;
        $this->email = $email;
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

    public function getDateNaissance(): DateTimeImmutable
    {
        return $this->dateNaissance;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
