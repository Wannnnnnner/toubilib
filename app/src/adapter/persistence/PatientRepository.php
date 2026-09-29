<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\domain\entities\Patient;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;
use toubilib\domain\exceptions\PatientNotFoundException;

class PatientRepository implements PatientRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $patientId): ?Patient
    {
        if (!Uuid::isValid($patientId)) {
            throw new PatientNotFoundException("Patient avec l'id : $patientId n'a pas été trouvé");
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