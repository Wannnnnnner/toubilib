<?php
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');
$builder->addDefinitions(__DIR__ . '/api.php');
$container = $builder->build();

$app = AppFactory::createFromContainer($container);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

$app = (require __DIR__ . '/routes.php')($app);
return $app;