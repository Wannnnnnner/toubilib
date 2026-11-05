<?php
use Slim\App;
use toubilib\adapter\api\actions\AnnulerRendezVousAction;

return function (App $app): App {
    $app->patch('/rdvs/{id}/annuler', AnnulerRendezVousAction::class);
    return $app;
};