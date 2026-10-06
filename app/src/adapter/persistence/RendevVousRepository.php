<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use toubilib\domain\entities\RendezVous;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\domain\entities\RendezVousStatus;
use toubilib\domain\exceptions\RendezVousNotFoundException;
use toubilib\adapters\persistence\RepositoryDatabaseErrorException;
use DateTimeImmutable;

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
        $stmt = $this->pdo->prepare("SELECT id,praticien_id, patient_id,  date_heure_debut, status, duree, date_creation, motif_visite
                                     FROM rdv
                                     WHERE id = :rendezVousId");
        $stmt->execute(['rendezVousId' => $rendezVousId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new RendezVousNotFoundException("Rendez-vous avec l'id : $rendezVousId n'a pas été trouvé");
        }
        $rendezVous = new RendezVous(
            id: $row['id'],
            praticienId: $row['praticien_id'],
            patientId: $row['patient_id'],
            dateHeureDebut: new DateTimeImmutable($row['date_heure_debut']),
            motifVisite: $row['motif_visite'],
            dateCreation: new DateTimeImmutable($row['date_creation']),
            status: RendezVousStatus::from($row['status']),
        );
        return $rendezVous;
    }

    public function save(RendezVous $rendezVous): void
    {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO rdv (id,praticien_id, patient_id,  date_heure_debut, status, duree, date_heure_fin, date_creation, motif_visite) 
                                              VALUES (:id, :idPrat, :idPatient, :dateHeureDebut, :status, :duree, :datefin, :dateCrea, :motif)
                                              ON CONFLICT (id) DO UPDATE SET 
                                              date_heure_debut = EXCLUDED.title, 
                                              status = EXCLUDED.status');
            $stmt->execute([
                'id' => $rendezVous->getId(),
                'idPrat' => $rendezVous->getPraticienId(),
                'idPatient' => $rendezVous->getPatientId(),
                'dateHeureDebut' => $rendezVous->getDateHeureDebut(),
                'status' => $rendezVous->getStatus()->value,
                'duree' => $rendezVous->getDuree(),
                'datefin' => $rendezVous->getDateHeureFin(),
                'dateCrea' => $rendezVous->getDateCreation(),
                'motif' => $rendezVous->getMotifVisite(),
            ]);
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException("Database Error : " . $e->getMessage());
        }
    }

    public function update(RendezVous $rendezVous): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE rdv
                                            SET status = :status 
                                            WHERE id = :id;");
            $stmt->execute([
                'id' => $rendezVous->getId(),
                'status' => $rendezVous->getStatus()->value,
            ]);
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException("Database Error : " . $e->getMessage());
        }
    }

}
