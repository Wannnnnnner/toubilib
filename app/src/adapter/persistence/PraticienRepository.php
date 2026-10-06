<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PraticienRepositoryInterface;
use toubilib\domain\entities\Praticien;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;
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


}