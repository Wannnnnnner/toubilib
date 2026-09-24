<?php
namespace jira\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\domain\entities\RendezVous;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVousStatus;
use toubilib\domain\exceptions\RendezVousNotFoundException;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;
use function PHPUnit\Framework\throwException;

class RendezVousRepository implements RendezVousRepositoryInterface
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $rendezVousId): ?RendezVous
    {
        if (!Uuid::isValid($rendezVousId)) {
            throw new RendezVousNotFoundException("Rendez-vous avec l'id : $rendezVousId n'a pas été trouvé");
        }
        $stmt = $this->pdo->prepare("SELECT id, date_heure_debut, status
                                     FROM rdv
                                     WHERE id = :rendezVousId");
        $stmt->execute(['rendezVousId' => $rendezVousId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new RendezVousNotFoundException("Rendez-vous avec l'id : $rendezVousId n'a pas été trouvé");
        }
        $rendezVous = new RendezVous($row['id'], $row['date_heure_debut'], RendezVousStatus::from($row['status']));
        return $rendezVous;
    }

    public function save(RendezVous $rendezVous): void
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO rdv (id, date_heure_debut, status) 
                                              VALUES (:id, :dateHeureDebut, :status)
                                              ON CONFLICT (id) DO UPDATE SET 
                                              date_heure_debut = EXCLUDED.title, 
                                              status = EXCLUDED.status');
            $stmt->execute([
                'id' => $rendezVous->getId(),
                'dateHeureDebut' => $rendezVous->getDateHeureDebut(),
                'status' => $rendezVous->getStatus()->value,
            ]);
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException("Database Error : " . $e->getMessage());
        }
    }

    public function update(RendezVous $rendezVous): void
    {

    }

}