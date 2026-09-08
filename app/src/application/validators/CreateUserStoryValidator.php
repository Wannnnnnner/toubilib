<?php
namespace toubilib\application\validators;

use toubilib\domain\entities\Owner;
use toubilib\application\dtos\CreateUserStoryDTO;
use toubilib\application\exceptions\ValidationException;
use toubilib\application\ports\spi\OwnerRepository;

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
