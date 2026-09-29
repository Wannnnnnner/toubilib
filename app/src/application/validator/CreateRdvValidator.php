<?php
namespace toubilib\application\validator;

use toubilib\adapters\persistence\PraticienRepository;
use toubilib\application\dtos\CreateRdvDTO;
use toubilib\adapters\persistence\PatientRepository;
use toubilib\application\exceptions\ValidationException;

class CreateRdvValidator
{

    public function __construct(
        private readonly PatientRepository $patientRepository,
        private readonly PraticienRepository $praticienRepository
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