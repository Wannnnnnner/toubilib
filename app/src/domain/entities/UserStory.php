<?php
namespace toubilib\domain\entities;

use Ramsey\Uuid\Uuid;
use toubilib\domain\exceptions\StatusChangeNotAllowedException;

class UserStory {
    private string $id;
    private string $title;
    private string $description;
    private UserStoryStatus $status;
    private Owner $owner;

    public function __construct(string $id, string $title, string $description, UserStoryStatus $status = UserStoryStatus::TODO) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public function assignTo(Owner $owner) {
        $this->owner = $owner;
    }

    public function start(): void {
        $this->changeStatusTo(UserStoryStatus::WIP);
    }

    public function finish(): void {
        $this->changeStatusTo(UserStoryStatus::DONE);
    }

    public function close(): void {
        $this->changeStatusTo(UserStoryStatus::CLOSED);
    }

    private function changeStatusTo(UserStoryStatus $newStatus): void {
        if (!$this->isStatusChangeAllowed($newStatus)) {
            throw new StatusChangeNotAllowedException("Cannot change state from " . $this->status->value . " to " . $newStatus->value);
        }
        $this->status = $newStatus;
    }

    private function isStatusChangeAllowed(UserStoryStatus $newStatus): bool {
        return !($newStatus == UserStoryStatus::TODO || 
                    ($newStatus == UserStoryStatus::WIP && $this->status != UserStoryStatus::TODO) || 
                    ($newStatus == UserStoryStatus::DONE && $this->status != UserStoryStatus::WIP) || 
                    ($newStatus == UserStoryStatus::CLOSED && $this->status != UserStoryStatus::DONE));
    }

    public function getId(): string {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getOwner(): Owner {
        return $this->owner;
    }

    public function getStatus(): UserStoryStatus {
        return $this->status;
    }

    public function updateTitle(string $title): void {
        $this->title = $title;
    }

    public function updateDescription(string $description): void {
        $this->description = $description;
    }
}