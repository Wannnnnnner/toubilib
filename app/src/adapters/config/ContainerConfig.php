<?php
namespace toubilib\adapters\config;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use jira\application\usecases\UserStoryService;
use jira\application\ports\api\UserStoryServiceInterface;
use jira\application\ports\spi\OwnerRepository;
use jira\application\ports\spi\UserStoryRepository;
use jira\adapters\persistence\PgUserStoryRepository;
use jira\adapters\persistence\PgOwnerRepository;
use jira\application\validators\CreateUserStoryValidator;

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
            'jira.pdo' => function (ContainerInterface $c) {
                //$config = parse_ini_file(__DIR__ . '/../../../config/jira.db.ini');
                $dsn = "{$_ENV['DRIVER']}:host={$_ENV['HOST']};dbname={$_ENV['DATABASE']}";
                $user = $_ENV['USERNAME'];
                $password = $_ENV['PASSWORD'];
                return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            },

            UserStoryRepository::class => \DI\create(PgUserStoryRepository::class)
                ->constructor(\DI\get('jira.pdo')),

            OwnerRepository::class => \DI\create(PgOwnerRepository::class)
                ->constructor(\DI\get('jira.pdo')),
        ]);
        return $builder->build();
    }
}