<?php

declare(strict_types=1);

namespace toubilib\application\ports\api;

use toubilib\application\dtos\PraticienDTO;

interface PraticienServiceInterface
{

    public function listerPraticiens(): array;
}