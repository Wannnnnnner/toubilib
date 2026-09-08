<?php
namespace toubilib\adapters\config;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use toubilib\application\usecases\UserStoryService;
use toubilib\application\ports\api\UserStoryServiceInterface;
use toubilib\application\ports\spi\OwnerRepository;
use toubilib\application\ports\spi\UserStoryRepository;
use toubilib\adapters\persistence\PgUserStoryRepository;
use toubilib\adapters\persistence\PgOwnerRepository;
use toubilib\application\validators\CreateUserStoryValidator;

final class ContainerConfig {

    public static function build(): \DI\Container {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
    
            // ── domain service ─
            UserStoryServiceInterface::class => \DI\create(UserStoryService::class)
                ->constructor(\DI\get(UserStoryRepository::class),\DI\get(OwnerRepository::class),\DI\get(CreateUserStoryValidator::class)),

            CreateUserStoryValidator::class => \DI\create(CreateUserStoryValidator::class)
                ->constructor(\DI\get(OwnerRepository::class)),

            // ── adapters ─
            'toubilib.pdo' => function (ContainerInterface $c) {
                //$config = parse_ini_file(__DIR__ . '/../../../config/toubilib.db.ini');
                $dsn = "{$_ENV['DRIVER']}:host={$_ENV['HOST']};dbname={$_ENV['DATABASE']}";
                $user = $_ENV['USERNAME'];
                $password = $_ENV['PASSWORD'];
                return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            },

            UserStoryRepository::class => \DI\create(PgUserStoryRepository::class)
                ->constructor(\DI\get('toubilib.pdo')),

            OwnerRepository::class => \DI\create(PgOwnerRepository::class)
                ->constructor(\DI\get('toubilib.pdo')),
        ]);
        return $builder->build();
    }
}