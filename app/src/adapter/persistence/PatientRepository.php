<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\domain\entities\Patient;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;

class PatientRepository implements PatientRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $patient): ?Patient
    {
    }


}