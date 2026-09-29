<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\domain\entities\Praticien;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;

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
            throw new PraticienNotFoundException("Patient avec l'id : $praticienId n'a pas été trouvé");
        }
        $stmt = $this->pdo->prepare("SELECT nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone
                                     FROM patient
                                     WHERE id = :patientId");
        $stmt->execute(['patientId' => $patientId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new PatientNotFoundException("Patient avec l'id : $patientId n'a pas été trouvé");
        }
        $rendezVous = new Patient($patientId, $row['nom'], $row['prenom'], $row['date_naissance'], $row['adresse'], $row['code_postal'], $row['ville'], $row['email'], $row['telephone']);
        return $rendezVous;
    }


}