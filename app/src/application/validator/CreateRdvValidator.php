<?php
namespace toubilib\application\validators;

use toubilib\application\dtos\CreateRdvDTO;

class CreateUserStoryValidator
{

    public function __construct(
    ) {
    }

    public function validate(CreateRdvDTO $dto): bool
    {
        $this->validatePatient($dto);
        $this->validatePraticien($dto);
    }

    public function validatePatient($dto)
    {

    }

    public function validatePraticien($dto)
    {

    }
}