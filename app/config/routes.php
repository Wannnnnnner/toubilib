<?php
use Slim\App;
use toubilib\adapter\controllers\actions\AnnulerRendezVousAction;
use toubilib\adapter\controllers\actions\ListerPraticiensAction;
use toubilib\adapter\controllers\actions\GetPraticienByIdAction;
use toubilib\adapter\controllers\actions\GetRDVByIdAction;

return function (App $app): App {
    $app->patch('/rdvs/{id}/annuler', AnnulerRendezVousAction::class);

    $app->get('/praticiens', ListerPraticiensAction::class)->setName('praticiens.lister');

    $app->get('/praticiens/{id}', GetPraticienByIdAction::class);

    $app->get('/rdvs/{id}', GetRDVByIdAction::class)->setName('rdv_detail');

    return $app;
};