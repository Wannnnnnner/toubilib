<?php
namespace tests\fakes;

use jira\domain\entities\UserStory;
use jira\domain\exceptions\UserStoryNotFoundException;
use jira\application\ports\spi\UserStoryRepository;

class InMemoryUserStoryRepository implements UserStoryRepository {

    private array $store = [];

    public function save(UserStory $userStory): void {
        $this->store[$userStory->getId()] = $userStory;
    }

    public function findById(string $id): UserStory {
        if (!isset($this->store[$id])) {
            throw new UserStoryNotFoundException("UserStory of id $id cannot be found");
        }
        return $this->store[$id];
    }

    public function findAll(): array {
        return array_values($this->store);
    }

    public function delete(string $id): void {
    if (!isset($this->store[$id])) {
        throw new UserStoryNotFoundException("UserStory of id $id cannot be found");
    }
    unset($this->store[$id]);
}
}