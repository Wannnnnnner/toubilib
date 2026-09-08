<?php
namespace toubilib\application\ports\api;

use toubilib\application\dtos\CreateUserStoryDTO;
use toubilib\application\dtos\UpdateUserStoryDTO;
use toubilib\application\dtos\UserStoryOutputDTO;

interface UserStoryServiceInterface {

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function start(string $id): void;

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function finish(string $id): void;

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function close(string $id): void;

    // ── @return UserStoryOutputDTO
    public function getUserStoryById(string $id): UserStoryOutputDTO;

    // ── @return UserStory[]
    public function getAllUserStories(): array;

    // ── @return UserStoryOutputDTO
    public function createUserStory(CreateUserStoryDTO $dto): UserStoryOutputDTO;

    // ── @throws UserStoryNotFoundException
    public function deleteUserStory(string $id): void;

    // ── @throws UserStoryNotFoundException
    public function updateUserStory(string $id, UpdateUserStoryDTO $dto): UserStoryOutputDTO;
}