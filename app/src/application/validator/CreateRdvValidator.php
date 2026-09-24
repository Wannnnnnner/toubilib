<?php
namespace toubilib\application\validator;

use toubilib\application\dtos\CreateRdvDTO;

class CreateRdvValidator
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