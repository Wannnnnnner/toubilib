<?php
use Psr\Container\ContainerInterface;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\api\PraticienServiceInterface;

use toubilib\adapter\controllers\actions\AnnulerRendezVousAction;
use toubilib\adapter\controllers\actions\ListerPraticiensAction;



return [
    AnnulerRendezVousAction::class => fn(ContainerInterface $c) =>
        new AnnulerRendezVousAction($c->get(RendezVousServiceInterface::class)),

    ListerPraticiensAction::class => fn($c) => new ListerPraticiensAction($c->get(PraticienServiceInterface::class)),
];