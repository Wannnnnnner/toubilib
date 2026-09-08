<?php
namespace toubilib\domain\entities;

use Ramsey\Uuid\Uuid;

class User {
    private string $id;
    private string $username;
    private string $passwordHash;

    public function __construct(string $id) {
        $this->id = $id;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username) {
        $this->username = $username;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash) {
        $this->passwordHash = $passwordHash;
    }
}