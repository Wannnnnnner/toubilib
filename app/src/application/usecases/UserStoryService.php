<?php
namespace toubilib\application\usecases;

use toubilib\application\dtos\UpdateUserStoryDTO;
use Ramsey\Uuid\Uuid;
use toubilib\domain\entities\UserStoryStatus;
use toubilib\domain\entities\UserStory;
use toubilib\application\ports\api\UserStoryServiceInterface;
use toubilib\application\ports\spi\UserStoryRepository;
use toubilib\application\ports\spi\OwnerRepository;
use toubilib\application\dtos\UserStoryOutputDTO;
use toubilib\application\dtos\CreateUserStoryDTO;
use toubilib\application\validators\CreateUserStoryValidator;
use toubilib\application\exceptions\ValidationException;

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
