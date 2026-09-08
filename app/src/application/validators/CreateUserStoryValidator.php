<?php
namespace toubilib\application\validators;

use jira\domain\entities\Owner;
use jira\application\dtos\CreateUserStoryDTO;
use jira\application\exceptions\ValidationException;
use jira\application\ports\spi\OwnerRepository;

class CreateUserStoryValidator {

    public function __construct(
        private readonly OwnerRepository $ownerRepository
    ) {}

    public function validate(CreateUserStoryDTO $dto): Owner {
        $errors = [];
        $owner = $this->ownerRepository->findById($dto->ownerId);
        if (!$owner) {
            $errors['ownerId'] = 'OwnerId does not match any existing user.';
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        return $owner;
    }
}
