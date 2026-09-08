<?php
namespace toubilib\application\ports\spi;

use jira\domain\entities\UserStory;

interface UserStoryRepository {

    // ── @return UserStory[]
    public function findAll(): array;

    // ── @return UserStory
    // ── @throws UserStoryNotFoundException
    public function findById(string $id): UserStory;

    // ── @throws UserStoryNotFoundException
    public function save(UserStory $userStory): void;

    // ── @throws UserStoryNotFoundException
    public function delete(string $uuid): void;

}