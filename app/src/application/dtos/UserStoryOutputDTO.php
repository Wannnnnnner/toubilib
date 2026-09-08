<?php
namespace toubilib\application\dtos;

use jira\domain\entities\UserStory;

final class UserStoryOutputDTO {
     
    private function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly array  $owner,
    ) {}

    public static function fromEntity(UserStory $userStory): self {
        return new self(
            id: $userStory->getId(),
            title: $userStory->getTitle(),
            description: $userStory->getDescription(),
            status: $userStory->getStatus()->value,
            owner: [
                'userId' => $userStory->getOwner()->getUserId(),
                'username' => $userStory->getOwner()->getUsername(),
            ],
        );
    }

    public static function listToArray(array $userStories): array {
        return array_map(
            static fn(UserStory $us) => self::fromEntity($us)->toArray(),
            $userStories
        );
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'owner' => $this->owner,
        ];
    }
}
