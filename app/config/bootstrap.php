<?php
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;

use toubilib\adapters\controllers\middlewares\Cors;
use toubilib\adapters\config\ContainerConfig;
use toubilib\adapters\controllers\actions\GetAllUserStoriesAction;
use toubilib\adapters\controllers\actions\GetUserStoryByIdAction;
use toubilib\adapters\controllers\actions\CreateUserStoryAction;
use toubilib\adapters\controllers\actions\DeleteUserStoryAction;
use toubilib\adapters\controllers\actions\PatchUserStoryAction;
use toubilib\adapters\controllers\actions\StartUserStoryAction;
use toubilib\adapters\controllers\actions\FinishUserStoryAction;
use toubilib\adapters\controllers\actions\CloseUserStoryAction;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ , 'toubilib.env');
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