<?php
use Psr\Container\ContainerInterface;
use toubilib\adapter\api\actions\AnnulerRendezVousAction;
use toubilib\application\ports\api\RendezVousServiceInterface;

return [
    AnnulerRendezVousAction::class => fn(ContainerInterface $c) =>
        new AnnulerRendezVousAction($c->get(RendezVousServiceInterface::class)),
];