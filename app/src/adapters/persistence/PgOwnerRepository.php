<?php
namespace toubilib\adapters\persistence;

use toubilib\domain\entities\Owner;
use toubilib\application\ports\spi\OwnerRepository;

class PgOwnerRepository implements OwnerRepository {

    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(string $id): ?Owner {
        $stmt = $this->pdo->prepare('SELECT uuid, username FROM owners WHERE uuid = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }
        
        $owner = new Owner($row['uuid'], $row['username']);
        return $owner;
    }

}