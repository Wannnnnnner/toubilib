<?php

use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\application\usecases\UserStoryService;
use toubilib\domain\entities\Owner;
use toubilib\domain\entities\UserStory;
use toubilib\domain\entities\UserStoryStatus;
use toubilib\adapters\controllers\actions\StartUserStoryAction;
use toubilib\adapters\controllers\actions\FinishUserStoryAction;
use toubilib\adapters\controllers\actions\CloseUserStoryAction;
use toubilib\application\validators\CreateUserStoryValidator;
use tests\fakes\InMemoryUserStoryRepository;
use tests\fakes\InMemoryOwnerRepository;

// ── Setup ─
const STORY_UUID = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
const UNKNOWN_UUID = 'f47ac10b-58cc-4372-a567-0e02b2c3d000';

function makeService(array $stories = []): UserStoryService {
    $repository = new InMemoryUserStoryRepository();
    $ownerRepository = new InMemoryOwnerRepository();
    $validator = new CreateUserStoryValidator($ownerRepository);
    foreach ($stories as $story) {
        $repository->save($story);
    }
    return new UserStoryService($repository, $ownerRepository, $validator);
}

function makeStartAction(array $stories = []): StartUserStoryAction {
    return new StartUserStoryAction(makeService($stories));
}

function makeFinishAction(array $stories = []): FinishUserStoryAction {
    return new FinishUserStoryAction(makeService($stories));
}

function makeCloseAction(array $stories = []): CloseUserStoryAction {
    return new CloseUserStoryAction(makeService($stories));
}

function makeChangeRequest(string $action): ServerRequestInterface {
    return (new RequestFactory())
        ->createRequest('POST', '/user-stories/' . STORY_UUID . '/' . $action);
}

function makeStoryForStatus(UserStoryStatus $status = UserStoryStatus::TODO): UserStory {
    $story = new UserStory(STORY_UUID, 'Correct title', 'Correct description', $status);
    $story->assignTo(new Owner('owner-uuid-1', 'alice'));
    return $story;
}

// ── StartUserStoryAction ─

it('should return 204 when starting a TODO user story', function () {
    $result = makeStartAction([makeStoryForStatus(UserStoryStatus::TODO)])(
        makeChangeRequest('start'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect($result->getStatusCode())->toBe(204);
});

it('should not return a body on a 204 when starting', function () {
    $result = makeStartAction([makeStoryForStatus(UserStoryStatus::TODO)])(
        makeChangeRequest('start'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect((string) $result->getBody())->toBe('');
});

it('should throw HttpNotFoundException when starting a non-existent user story', function () {
    expect(fn () => makeStartAction([])(
        makeChangeRequest('start'),
        (new ResponseFactory())->createResponse(),
        ['id' => UNKNOWN_UUID]
    ))->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should throw HttpBadRequestException when starting a user story that is not TODO', function () {
    expect(fn () => makeStartAction([makeStoryForStatus(UserStoryStatus::WIP)])(
        makeChangeRequest('start'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    ))->toThrow(\Slim\Exception\HttpBadRequestException::class);
});

// ── FinishUserStoryAction ─

it('should return 204 when finishing a WIP user story', function () {
    $result = makeFinishAction([makeStoryForStatus(UserStoryStatus::WIP)])(
        makeChangeRequest('finish'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect($result->getStatusCode())->toBe(204);
});

it('should not return a body on a 204 when finishing', function () {
    $result = makeFinishAction([makeStoryForStatus(UserStoryStatus::WIP)])(
        makeChangeRequest('finish'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect((string) $result->getBody())->toBe('');
});

it('should throw HttpNotFoundException when finishing a non-existent user story', function () {
    expect(fn () => makeFinishAction([])(
        makeChangeRequest('finish'),
        (new ResponseFactory())->createResponse(),
        ['id' => UNKNOWN_UUID]
    ))->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should throw HttpBadRequestException when finishing a user story that is not WIP', function () {
    expect(fn () => makeFinishAction([makeStoryForStatus(UserStoryStatus::TODO)])(
        makeChangeRequest('finish'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    ))->toThrow(\Slim\Exception\HttpBadRequestException::class);
});

// ── CloseUserStoryAction ─

it('should return 204 when closing a DONE user story', function () {
    $result = makeCloseAction([makeStoryForStatus(UserStoryStatus::DONE)])(
        makeChangeRequest('close'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect($result->getStatusCode())->toBe(204);
});

it('should not return a body on a 204 when closing', function () {
    $result = makeCloseAction([makeStoryForStatus(UserStoryStatus::DONE)])(
        makeChangeRequest('close'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    );
    expect((string) $result->getBody())->toBe('');
});

it('should throw HttpNotFoundException when closing a non-existent user story', function () {
    expect(fn () => makeCloseAction([])(
        makeChangeRequest('close'),
        (new ResponseFactory())->createResponse(),
        ['id' => UNKNOWN_UUID]
    ))->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should throw HttpBadRequestException when closing a user story that is not DONE', function () {
    expect(fn () => makeCloseAction([makeStoryForStatus(UserStoryStatus::TODO)])(
        makeChangeRequest('close'),
        (new ResponseFactory())->createResponse(),
        ['id' => STORY_UUID]
    ))->toThrow(\Slim\Exception\HttpBadRequestException::class);
});