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

    public function findById(string $praticien): ?Praticien
    {
    }


}