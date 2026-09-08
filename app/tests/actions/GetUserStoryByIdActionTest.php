<?php

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use jira\domain\entities\UserStory;
use jira\domain\entities\Owner;
use jira\domain\entities\UserStoryStatus;
use jira\adapters\controllers\actions\GetUserStoryByIdAction;
use jira\application\usecases\UserStoryService;
use jira\application\validators\CreateUserStoryValidator;
use tests\fakes\InMemoryUserStoryRepository;
use tests\fakes\InMemoryOwnerRepository;

// ── Setup ─
function makeGetAction(array $stories = []): GetUserStoryByIdAction {
    $repository = new InMemoryUserStoryRepository();
    $ownerRepository = new InMemoryOwnerRepository();
    $validator = new CreateUserStoryValidator($ownerRepository);
    foreach ($stories as $story) {
        $repository->save($story);
    }
    $service = new UserStoryService($repository, $ownerRepository, $validator);
    return new GetUserStoryByIdAction($service);
}

function makeGetRequestResponse(): array {
    $request  = (new RequestFactory())->createRequest('GET', '/user-stories/f47ac10b-58cc-4372-a567-0e02b2c3d479');
    $response = (new ResponseFactory())->createResponse();
    return [$request, $response];
}

function makeStory(): UserStory {
    $userStory = new UserStory('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'Correct title', 'Correct description', UserStoryStatus::TODO);
    $userStory->assignTo(new Owner('owner-uuid-1', 'alice'));
    return $userStory;
}

// ── Tests ─
it('should return 200 with an existing user story in JSON', function () {
    [$request, $response] = makeGetRequestResponse();
    $result = makeGetAction([makeStory()])($request, $response, ['id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479']);
    expect($result->getStatusCode())->toBe(200);
    $body = json_decode((string) $result->getBody(), true);
    expect($body)->toMatchArray([
        'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
        'title' => 'Correct title',
        'description' => 'Correct description',
        'status' => 'TODO',
        'owner' => [
            'userId' => 'owner-uuid-1',
            'username' => 'alice',
        ],
    ]);
});

it('should return 404 if the user story is not found', function () {
    [$request, $response] = makeGetRequestResponse();
    expect(fn() => makeGetAction([])($request, $response, ['id' => '99']))
        ->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should return a Content-Type of application/json', function () {
    [$request, $response] = makeGetRequestResponse();
    $result = makeGetAction([makeStory()])($request, $response, ['id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479']);
    expect($result->getHeaderLine('Content-Type'))->toBe('application/json;charset=utf-8');
});