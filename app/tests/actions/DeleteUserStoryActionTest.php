<?php

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use jira\application\usecases\UserStoryService;
use jira\domain\entities\UserStory;
use jira\domain\entities\UserStoryStatus;
use jira\adapters\controllers\actions\DeleteUserStoryAction;
use jira\application\validators\CreateUserStoryValidator;
use tests\fakes\InMemoryUserStoryRepository;
use tests\fakes\InMemoryOwnerRepository;

// ── Setup ─
function makeDeleteAction(array $stories = []): DeleteUserStoryAction {
    $repository = new InMemoryUserStoryRepository();
    $ownerRepository = new InMemoryOwnerRepository();
    $validator = new CreateUserStoryValidator($ownerRepository);
    foreach ($stories as $story) {
        $repository->save($story);
    }
    $service = new UserStoryService($repository, $ownerRepository, $validator);
    return new DeleteUserStoryAction($service);
}

function makeDeleteRequest(): \Psr\Http\Message\ServerRequestInterface {
    return (new RequestFactory())->createRequest('DELETE', '/user-stories/f47ac10b-58cc-4372-a567-0e02b2c3d479');
}

function makeDeleteStory(): UserStory {
    $story = new UserStory('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Correct title', 'Correct description', UserStoryStatus::TODO);
    return $story;
}

// ── Tests ─
it('should return 204 and delete the user story', function () {
    $result = makeDeleteAction([makeDeleteStory()])(
        makeDeleteRequest(),
        (new ResponseFactory())->createResponse(),
        ['id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479']
    );
    expect($result->getStatusCode())->toBe(204);
});

it('should return 404 if the user story is not found', function () {
    expect(fn() => makeDeleteAction([])(
        makeDeleteRequest(),
        (new ResponseFactory())->createResponse(),
        ['id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d000']
    ))->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should not return a body on a 204', function () {
    $result = makeDeleteAction([makeDeleteStory()])(
        makeDeleteRequest(),
        (new ResponseFactory())->createResponse(),
        ['id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479']
    );
    expect((string) $result->getBody())->toBe('');
});
