<?php

declare(strict_types=1);

namespace toubilib\domain\entities;

enum RendezVousStatus: int
{
    case PLANIFIE = 0;
    case HONORE = 1;
    case IGNORE = 2;

    case ANNULE = 3;
}