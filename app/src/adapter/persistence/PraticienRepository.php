<?php
namespace toubilib\adapter\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\domain\entities\Praticien;
use toubilib\adapter\persistence\RepositoryDatabaseErrorException;
use toubilib\domain\exceptions\PraticienNotFoundException;
use toubilib\domain\entities\Specialite;

class PraticienRepository implements PraticienRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $praticienId): ?Praticien
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.titre, p.nom, p.prenom, p.ville, p.email, p.telephone,
                p.rpps_id, p.structure_id, p.nouveau_patient,
                s.id AS spe_id, s.libelle AS spe_libelle, s.description AS spe_description
         FROM praticien p
         JOIN specialite s ON s.id = p.specialite_id
         WHERE p.id = :id'
        );
        $stmt->execute(['id' => $praticienId]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($r === false) {
            throw new PraticienNotFoundException("Praticien $praticienId not found");
        }

        return new Praticien(
            $r['id'],
            $r['titre'],
            $r['nom'],
            $r['prenom'],
            $r['ville'],
            $r['email'],
            $r['telephone'],
            $r['rpps_id'],
            $r['structure_id'],
            $r['nouveau_patient'] === '1',          // colonne bit(1) : PDO renvoie '0'/'1'
            new Specialite((int) $r['spe_id'], $r['spe_libelle'], $r['spe_description'])
        );
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, nom, prenom, ville, email, telephone, rpps_id, titre, structure_id,
                   nouveau_patient::int AS nouveau_patient
            FROM praticien
            ORDER BY nom, prenom';

        $praticiens = [];
        foreach ($this->pdo->query($sql, \PDO::FETCH_ASSOC) as $row) {
            $praticiens[] = new Praticien(
                $row['id'],
                $row['titre'],
                $row['nom'],
                $row['prenom'],
                $row['ville'],
                $row['email'],
                $row['telephone'],
                $row['rpps_id'] ?? '',
                $row['structure_id'] ?? '',
                (bool) $row['nouveau_patient'],
                NULL // Specialite is not loaded in this query
            );
        }
        return $praticiens;
    }


}