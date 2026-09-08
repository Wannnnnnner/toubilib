<?php

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use toubilib\application\usecases\UserStoryService;
use toubilib\domain\entities\Owner;
use toubilib\domain\entities\UserStory;
use toubilib\domain\entities\UserStoryStatus;
use toubilib\adapters\controllers\actions\GetAllUserStoriesAction;
use toubilib\application\validators\CreateUserStoryValidator;
use tests\fakes\InMemoryUserStoryRepository;
use tests\fakes\InMemoryOwnerRepository;

// ── Setup ─

function makeGetAllAction(array $stories = []): GetAllUserStoriesAction {
    $repository = new InMemoryUserStoryRepository();
    $ownerRepository = new InMemoryOwnerRepository();
    $validator = new CreateUserStoryValidator($ownerRepository);
    foreach ($stories as $story) {
        $repository->save($story);
    }
    $service = new UserStoryService($repository, $ownerRepository, $validator);
    return new GetAllUserStoriesAction($service);
}

function makeGetAllRequest(): \Psr\Http\Message\ServerRequestInterface {
    return (new RequestFactory())->createRequest('GET', '/user-stories');
}

function makeStoryWithOwner(string $id, string $title, UserStoryStatus $status = UserStoryStatus::TODO): UserStory {
    $story = new UserStory($id, $title, 'Correct description', $status);
    $story->assignTo(new Owner('owner-uuid-1', 'alice'));
    return $story;
}

// ── Tests ─

it('should return 200', function () {
    $result = makeGetAllAction()(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    expect($result->getStatusCode())->toBe(200);
});

it('should return a Content-Type of application/json', function () {
    $result = makeGetAllAction()(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    expect($result->getHeaderLine('Content-Type'))->toBe('application/json;charset=utf-8');
});

it('should return an empty array if there are no user stories', function () {
    $result = makeGetAllAction()(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    $body = json_decode((string) $result->getBody(), true);
    expect($body)->toBe([]);
});

it('should return all user stories as a JSON array', function () {
    $stories = [
        makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d001', 'First US'),
        makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d002', 'Second US'),
    ];
    $result = makeGetAllAction($stories)(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    $body = json_decode((string) $result->getBody(), true);
    expect($body)->toHaveCount(2);
});

it('should serialize each user story with the expected fields', function () {
    $story = makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d001', 'First US');
    $result = makeGetAllAction([$story])(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    $body = json_decode((string) $result->getBody(), true);
    expect($body[0])->toMatchArray([
        'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d001',
        'title' => 'First US',
        'description' => 'Correct description',
        'status' => 'TODO',
        'owner' => ['userId' => 'owner-uuid-1', 'username' => 'alice'],
    ]);
});

it('should serialize the status as a string, not an object', function () {
    $story = makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d001', 'First US', UserStoryStatus::WIP);
    $result = makeGetAllAction([$story])(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    $body = json_decode((string) $result->getBody(), true);
    expect($body[0]['status'])->toBe('WIP');
});

it('should return stories in insertion order', function () {
    $stories = [
        makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d001', 'First US'),
        makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d002', 'Second US'),
        makeStoryWithOwner('f47ac10b-58cc-4372-a567-0e02b2c3d003', 'Third US'),
    ];
    $result = makeGetAllAction($stories)(
        makeGetAllRequest(),
        (new ResponseFactory())->createResponse(),
        []
    );
    $body = json_decode((string) $result->getBody(), true);
    expect($body[0]['title'])->toBe('First US')
        ->and($body[1]['title'])->toBe('Second US')
        ->and($body[2]['title'])->toBe('Third US');
});