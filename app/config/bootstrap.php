<?php
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;

use jira\adapters\controllers\middlewares\Cors;
use jira\adapters\config\ContainerConfig;
use jira\adapters\controllers\actions\GetAllUserStoriesAction;
use jira\adapters\controllers\actions\GetUserStoryByIdAction;
use jira\adapters\controllers\actions\CreateUserStoryAction;
use jira\adapters\controllers\actions\DeleteUserStoryAction;
use jira\adapters\controllers\actions\PatchUserStoryAction;
use jira\adapters\controllers\actions\StartUserStoryAction;
use jira\adapters\controllers\actions\FinishUserStoryAction;
use jira\adapters\controllers\actions\CloseUserStoryAction;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ , 'jira.env');
$dotenv->load();
// ── conteneur d'injection de dépendances ─
$container = ContainerConfig::build();
$app = AppFactory::createFromContainer($container);

// ── ajout middlewares ─
$app->addBodyParsingMiddleware();
$app->add(Cors::class);
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', function ($exception, $displayErrorDetails) {
    return json_encode(['message' => $exception->getMessage()]);
});

// ── ajout des routes ─
$app->group('/user-stories', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('', GetAllUserStoriesAction::class)
        ->setName('user-stories-list');
    $group->get('/{id}', GetUserStoryByIdAction::class)
        ->setName('user-story-get');
    $group->post('/{id}/start', StartUserStoryAction::class)
        ->setName('user-story-start');
    $group->post('/{id}/finish', FinishUserStoryAction::class)
        ->setName('user-story-finish');
    $group->post('/{id}/close', CloseUserStoryAction::class)
        ->setName('user-story-close');
    $group->post('', CreateUserStoryAction::class)
        ->setName('user-story-create');
    $group->delete('/{id}', DeleteUserStoryAction::class)
        ->setName('user-story-delete');
    $group->patch('/{id}', PatchUserStoryAction::class)
        ->setName('user-story-patch');
});
// CORS : options pour les requêtes preflight
$app->options('/{routes:.+}', function (Request $request, Response $response): Response {
    return $response;
});
$routeParser = $app->getRouteCollector()->getRouteParser();

return $app;