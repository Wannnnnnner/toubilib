<?php

declare(strict_types=1);

namespace toubilib\application\ports\api;

use toubilib\application\dtos\PraticienDetailDTO;

interface PraticienServiceInterface
{
    public function getPraticien(string $id): PraticienDetailDTO;

    public function listerPraticiens(): array;
}