<?php
namespace tests\fakes;

use jira\domain\entities\Owner;
use jira\application\ports\spi\OwnerRepository;

class InMemoryOwnerRepository implements OwnerRepository {

    private array $store = [];

    public function save(Owner $owner): void {
        $this->store[$owner->getUserId()] = $owner;
    }

    public function findById(string $id): ?Owner {
        return $this->store[$id] ?? null;
    }
}