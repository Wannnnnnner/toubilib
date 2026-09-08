<?php
namespace toubilib\application\usecases;

use jira\application\dtos\UpdateUserStoryDTO;
use Ramsey\Uuid\Uuid;
use jira\domain\entities\UserStoryStatus;
use jira\domain\entities\UserStory;
use jira\application\ports\api\UserStoryServiceInterface;
use jira\application\ports\spi\UserStoryRepository;
use jira\application\ports\spi\OwnerRepository;
use jira\application\dtos\UserStoryOutputDTO;
use jira\application\dtos\CreateUserStoryDTO;
use jira\application\validators\CreateUserStoryValidator;
use jira\application\exceptions\ValidationException;

class UserStoryService implements UserStoryServiceInterface {

    public function __construct(
        private UserStoryRepository $userStoryRepository,
        private OwnerRepository $ownerRepository,
        private CreateUserStoryValidator $validator,
    ) {}

    // ── @return UserStory[]
    public function getAllUserStories(): array {
        return $this->userStoryRepository->findAll();
    }

    // ── @return UserStoryOutputDTO
    public function getUserStoryById(string $id): UserStoryOutputDTO {
        $userStory = $this->userStoryRepository->findById($id);
        return UserStoryOutputDTO::fromEntity($userStory);
    }

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function start(string $id): void {
        $userStory = $this->userStoryRepository->findById($id);
        $userStory->start();
        $this->userStoryRepository->save($userStory);
    }

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function finish(string $id): void {
        $userStory = $this->userStoryRepository->findById($id);
        $userStory->finish();
        $this->userStoryRepository->save($userStory);
    }

    // ── @throws UserStoryNotFoundException
    // ── @throws StatusChangeNotAllowedException
    public function close(string $id): void {
        $userStory = $this->userStoryRepository->findById($id);
        $userStory->close();
        $this->userStoryRepository->save($userStory);
    }

    // ── @return UserStoryOutputDTO
    // ── @throws PersistenceException
    public function createUserStory(CreateUserStoryDTO $dto): UserStoryOutputDTO {
        try {
            $owner = $this->validator->validate($dto);
        } catch (ValidationException $e) {
            throw new \DomainException("Owner {$dto->ownerId} can not be found.");
        }
        $userStory = new UserStory(id: Uuid::uuid4()->toString(),
            title: $dto->title, description: $dto->description,
            status: UserStoryStatus::TODO
        );
        $userStory->assignTo($owner);
        $this->userStoryRepository->save($userStory);
        return UserStoryOutputDTO::fromEntity($userStory);
    } 

    // ── @throws UserStoryNotFoundException
    public function deleteUserStory(string $id): void {
        $this->userStoryRepository->delete($id);   
    }

    // ── @return UserStoryOutputDTO
    // ── @throws UserStoryNotFoundException
    public function updateUserStory(string $id, UpdateUserStoryDTO $dto): UserStoryOutputDTO {
        $userStory = $this->userStoryRepository->findById($id);

        if ($dto->title !== null) {
            $userStory->updateTitle($dto->title);
        }
        if ($dto->description !== null) {
            $userStory->updateDescription($dto->description);
        }

        $this->userStoryRepository->save($userStory);
        return UserStoryOutputDTO::fromEntity($userStory);
    }        
        
}
