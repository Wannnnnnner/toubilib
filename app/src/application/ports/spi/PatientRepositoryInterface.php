<?php

declare(strict_types=1);

namespace toubilib\application\ports\spi;

use toubilib\domain\entities\Patient;

interface PatientRepositoryInterface
{
    public function findById(string $patient): ?Patient;

    public function save(Patient $patient): void;

    public function update(Patient $patient): void;
}