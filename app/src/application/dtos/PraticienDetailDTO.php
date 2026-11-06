<?php
namespace toubilib\application\dtos;

use toubilib\domain\entities\Praticien;

class PraticienDetailDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $titre,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $specialite,
        public readonly ?string $specialite_description,
        public readonly string $ville,
        public readonly string $email,
        public readonly string $telephone,
        public readonly ?string $rpps_id,
        public readonly ?string $structure_id,
        public readonly bool $accepte_nouveau_patient
    ) {
    }

    public static function fromEntity(Praticien $p): self
    {
        return new self(
            $p->getId(),
            $p->getTitre(),
            $p->getNom(),
            $p->getPrenom(),
            $p->specialite->getLibelle(),
            $p->specialite->getDescription(),
            $p->getVille(),
            $p->getEmail(),
            $p->getTelephone(),
            $p->getRppsId(),
            $p->getVille(),
            $p->accepteNouveauPatient()
        );
    }
}