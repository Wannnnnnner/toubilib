<?php
namespace toubilib\adapters\persistence;

use Ramsey\Uuid\Uuid;
use jira\domain\entities\Owner;
use jira\domain\entities\UserStory;
use jira\domain\entities\UserStoryStatus;
use jira\domain\exceptions\UserStoryNotFoundException;
use jira\application\ports\spi\UserStoryRepository;
use jira\adapters\persistence\RepositoryDatabaseErrorException;

class PgUserStoryRepository implements UserStoryRepository {

    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(UserStory $userStory): void {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO userstory (uuid, title, description, status, owner_uuid) 
                                              VALUES (:uuid, :title, :description, :status, :owner_uuid)
                                              ON CONFLICT (uuid) DO UPDATE SET 
                                              title = EXCLUDED.title, 
                                              description = EXCLUDED.description, 
                                              status = EXCLUDED.status, 
                                              owner_uuid = EXCLUDED.owner_uuid');
            $stmt->execute([
                'uuid' => $userStory->getId(),
                'title' => $userStory->getTitle(),
                'description' => $userStory->getDescription(),
                'status' => $userStory->getStatus()->value,
                'owner_uuid' => $userStory->getOwner() ? $userStory->getOwner()->getUserId() : null
            ]);
        } catch (\PDOException $e) {
            throw new RepositoryDatabaseErrorException("Database Error : ".$e->getMessage());
        }
    }

    public function findAll(): array {
        $stmt = $this->pdo->prepare('SELECT us.uuid, us.title, us.description, us.status, o.uuid AS owner_uuid, o.username 
                                      FROM userstory us 
                                      LEFT JOIN owners o ON us.owner_uuid = o.uuid');
        $stmt->execute();

        $userStories = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $owner = $row['owner_uuid'] ? new Owner($row['owner_uuid'], $row['username']) : null;
            $userStory = new UserStory($row['uuid'], $row['title'], $row['description'],UserStoryStatus::from($row['status']));
            $userStory->assignTo($owner);
            $userStories[] = $userStory;
        }
        return $userStories;
    }

    public function findById(string $uuid): UserStory {
        if (!Uuid::isValid($uuid)) {
            throw new UserStoryNotFoundException("UserStory of id " . $uuid . " is not valid");
        }
        $stmt = $this->pdo->prepare('SELECT us.uuid, us.title, us.description, us.status, o.uuid AS owner_uuid, o.username 
                                  FROM userstory us 
                                  LEFT JOIN owners o ON us.owner_uuid = o.uuid 
                                  WHERE us.uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            throw new UserStoryNotFoundException("UserStory of id " . $uuid . " cannot be found");
        }

        $owner = $row['owner_uuid'] ? new Owner($row['owner_uuid'], $row['username']) : null;
        $userStory = new UserStory($row['uuid'], $row['title'], $row['description'], UserStoryStatus::from($row['status']));
        $userStory->assignTo($owner);
        return $userStory;
    }

    public function delete(string $uuid): void {
        $stmt = $this->pdo->prepare('DELETE FROM userstory WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $uuid]);
    }
}