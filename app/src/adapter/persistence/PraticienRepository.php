<?php
namespace toubilib\adapter\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\domain\entities\Praticien;
use toubilib\adapter\persistence\RepositoryDatabaseErrorException;
use toubilib\domain\exceptions\PraticienNotFoundException;

class PraticienRepository implements PraticienRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $praticienId): ?Praticien
    {
        if (!Uuid::isValid($praticienId)) {
            throw new PraticienNotFoundException("Praticien avec l'id : $praticienId n'a pas été trouvé");
        }
        $stmt = $this->pdo->prepare("SELECT nom, prenom, ville, email, telephone, rppsId, titre, accepteNouveauPatient
                                     FROM praticien
                                     WHERE id = :praticienId");
        $stmt->execute(['praticienId' => $praticienId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new PraticienNotFoundException("praticien avec l'id : $praticienId n'a pas été trouvé");
        }
        $praticien = new Praticien($praticienId, $row['nom'], $row['prenom'], $row['ville'], $row['email'], $row['telephone'], $row['rppsId'], $row['titre'], $row['$accepteNouveauPatient']);
        return $praticien;
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, nom, prenom, ville, email, telephone, rpps_id, titre,
                   nouveau_patient::int AS nouveau_patient
            FROM praticien
            ORDER BY nom, prenom';

        $praticiens = [];
        foreach ($this->pdo->query($sql, \PDO::FETCH_ASSOC) as $row) {
            $praticiens[] = new Praticien(
                $row['id'],
                $row['nom'],
                $row['prenom'],
                $row['ville'],
                $row['email'],
                $row['telephone'],
                $row['rpps_id'] ?? '',
                $row['titre'],
                (bool) $row['nouveau_patient']
            );
        }
        return $praticiens;
    }


}