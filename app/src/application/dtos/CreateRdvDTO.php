<?php

declare(strict_types=1);

namespace toubilib\application\dtos;

use DateTimeImmutable;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use toubilib\application\exceptions\ValidationException;

class CreateRdvDTO
{
    public readonly string $idPatient;
    public readonly string $idMedecin;
    public readonly string $motif;
    public readonly DateTimeImmutable $dateHeure;

    private function __construct(array $data)
    {
        $this->idPatient = (string) $data['idPatient'];
        $this->idMedecin = (string) $data['idMedecin'];
        $this->motif = $data['Motif'];
        $this->dateHeure = new DateTimeImmutable($data['DateHeure']);
    }

    public static function fromArray(array $data): self
    {
        try {
            v::key('idPatient', v::stringType()->notEmpty())
                ->key('idMedecin', v::stringType()->notEmpty())
                ->key('Motif', v::stringType()->notEmpty())
                ->key('DateHeure', v::dateTime())
                ->assert($data);
        } catch (NestedValidationException $e) {
            throw new ValidationException('Données de création de rendez-vous invalides.', 0, $e);
        }

        // Sanitization du motif
        if (isset($data['Motif'])) {
            $data['Motif'] = filter_var($data['Motif'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        return new self($data);
    }
}