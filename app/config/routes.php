<?php
use Slim\App;
use toubilib\adapter\controllers\actions\AnnulerRendezVousAction;
use toubilib\adapter\controllers\actions\ListerPraticiensAction;

return function (App $app): App {
    $app->patch('/rdvs/{id}/annuler', AnnulerRendezVousAction::class);

    $app->get('/praticiens', ListerPraticiensAction::class)->setName('praticiens.lister');

    return $app;
};