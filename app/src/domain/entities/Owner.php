<?php
namespace jira\domain\entities;

class Owner {
    public function __construct(
        private string $userId,
        private string $username,
    ) {}

    public function getUserId(): string {
        return $this->userId;
    }

    public function getUsername(): string {
        return $this->username;
    }
}