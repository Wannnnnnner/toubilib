<?php
namespace toubilib\application\validator;

use toubilib\application\dtos\CreateRdvDTO;
use toubilib\application\exceptions\ValidationException;
use toubilib\application\ports\spi\PatientRepositoryInterface;
use toubilib\application\ports\spi\PraticienRepositoryInterface;

class CreateRdvValidator
{


    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly PraticienRepositoryInterface $praticienRepository
    ) {
    }

    public function validate(CreateRdvDTO $dto): bool
    {
        $resPat = $this->validatePatient($dto);
        $resPrat = $this->validatePraticien($dto);
        return $resPat and $resPrat;
    }

    public function validatePatient(CreateRdvDTO $dto): bool
    {
        $res = false;
        $owner = $this->patientRepository->findById($dto->idPatient);
        if (!$owner) {
            throw new ValidationException("Le patient n'existe pas");
        } else {
            $res = true;
        }
        return $res;
    }

    public function validatePraticien(CreateRdvDTO $dto): bool
    {
        $res = false;
        $owner = $this->praticienRepository->findById($dto->idMedecin);
        if (!$owner) {
            throw new ValidationException("Le praticien n'existe pas");
        } else {
            $res = true;
        }
        return $res;
    }
}