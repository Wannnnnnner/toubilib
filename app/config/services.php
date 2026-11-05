<?php
use Psr\Container\ContainerInterface;
use toubilib\adapter\persistence\PatientRepository;
use toubilib\adapter\persistence\PraticienRepository;
use toubilib\adapter\persistence\RendezVousRepository;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\application\usecases\RendezVousService;
use toubilib\application\validator\CreateRdvValidator;

return [
    'pdo.toubilib' => function (ContainerInterface $c) {
        $cfg = parse_ini_file($c->get('db.config'));
        $dsn = "{$cfg['lib.driver']}:host={$cfg['lib.host']};dbname={$cfg['lib.database']}";
        $pdo = new \PDO($dsn, $cfg['lib.username'], $cfg['lib.password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },

    PatientRepository::class => fn(ContainerInterface $c) =>
        new PatientRepository($c->get('pdo.toubilib')),

    PraticienRepository::class => fn(ContainerInterface $c) =>
        new PraticienRepository($c->get('pdo.toubilib')),

    RendezVousRepositoryInterface::class => fn(ContainerInterface $c) =>
        new RendezVousRepository($c->get('pdo.toubilib')),

    CreateRdvValidator::class => fn(ContainerInterface $c) =>
        new CreateRdvValidator(
            $c->get(PatientRepository::class),
            $c->get(PraticienRepository::class)
        ),

    RendezVousServiceInterface::class => fn(ContainerInterface $c) =>
        new RendezVousService(
            $c->get(CreateRdvValidator::class),
            $c->get(RendezVousRepositoryInterface::class)
        ),
];