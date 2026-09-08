<?php

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\application\usecases\UserStoryService;
use toubilib\application\validators\CreateUserStoryValidator;
use toubilib\domain\entities\Owner;
use toubilib\adapters\controllers\actions\CreateUserStoryAction;
use tests\fakes\InMemoryUserStoryRepository;
use tests\fakes\InMemoryOwnerRepository;

// ── Setup ─
function makeCreateAction(array $owners = []): CreateUserStoryAction {
    $repository = new InMemoryUserStoryRepository();
    $ownerRepository = new InMemoryOwnerRepository();
    foreach ($owners as $owner) {
        $ownerRepository->save($owner);
    }
    $validator = new CreateUserStoryValidator($ownerRepository);
    $service = new UserStoryService($repository, $ownerRepository, $validator);
    return new CreateUserStoryAction($service);
}

function makePostRequest(array $body): ServerRequestInterface {
    $request = (new RequestFactory())->createRequest('POST', '/user-stories');
    $stream = (new \Slim\Psr7\Factory\StreamFactory())->createStream(json_encode($body));
    return $request
        ->withHeader('Content-Type', 'application/json')
        ->withBody($stream)
        ->withParsedBody($body);
}

function makeResponse(): ResponseInterface {
    return (new ResponseFactory())->createResponse();
}

// ── Tests ─
it('should return 201 with the JSON body and create a user story', function () {
    $owner  = new Owner('owner-uuid-1', 'alice');
    $result = makeCreateAction([$owner])(
        makePostRequest([
            'title' => 'Correct title',
            'description' => 'Correct description',
            'ownerId' => 'owner-uuid-1',
        ]),
        makeResponse(),
        []
    );
    expect($result->getStatusCode())->toBe(201);
    $body = json_decode((string) $result->getBody(), true);
    expect($body)->toMatchArray([
        'title' => 'Correct title',
        'description' => 'Correct description',
        'status' => 'TODO',
        'owner' => ['userId' => 'owner-uuid-1', 'username' => 'alice'],
    ]);
});

it('should return 422 with errors if validation fails', function () {
    $result = makeCreateAction()(
        makePostRequest(['title' => 'ab', 'description' => 'short', 'ownerId' => 'owner-uuid-1']),
        makeResponse(),
        []
    );
    expect($result->getStatusCode())->toBe(422);
    $body = json_decode((string) $result->getBody(), true);
    expect($body['errors'])->toHaveKeys(['title', 'description']);
});

it('should return a Content-Type of application/json', function () {
    $owner  = new Owner('1', 'alice');
    $result = makeCreateAction([$owner])(
        makePostRequest([
            'title' => 'Correct title',
            'description' => 'Correct description',
            'ownerId' => '1',
        ]),
        makeResponse(),
        []
    );
    expect($result->getHeaderLine('Content-Type'))->toBe('application/json;charset=utf-8');
});

it('should return 422 if the body is empty', function () {
    $result = makeCreateAction()(
        makePostRequest([]),
        makeResponse(),
        []
    );
    expect($result->getStatusCode())->toBe(422);
});

