<?php
use Psr\Container\ContainerInterface;
use toubilib\adapter\persistence\PDORendezVousRepository;
use toubilib\application\ports\api\RendezVousServiceInterface;
use toubilib\application\ports\spi\RendezVousRepositoryInterface;
use toubilib\application\usecases\RendezVousService;

return [
    'pdo.toubilib' => function (ContainerInterface $c) {
        $cfg = parse_ini_file($c->get('.env'));
        $dsn = "{$cfg['driver']}:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']}";
        $pdo = new \PDO($dsn, $cfg['user'], $cfg['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },

    RendezVousRepositoryInterface::class => fn(ContainerInterface $c) =>
        new RendezVousRepository($c->get('pdo.toubilib')),

    RendezVousServiceInterface::class => fn(ContainerInterface $c) =>
        new RendezVousService($c->get(RendezVousRepositoryInterface::class)),
];