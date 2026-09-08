<?php

use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use tests\fakes\InMemoryUserStoryRepository;
use toubilib\application\usecases\UserStoryService;
use toubilib\domain\entities\UserStory;
use toubilib\domain\entities\UserStoryStatus;
use toubilib\domain\entities\Owner;
use toubilib\adapters\controllers\actions\PatchUserStoryAction;
use toubilib\application\validators\CreateUserStoryValidator;

// ── Setup ─
function makeFakeUserStory(string $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479'): UserStory {
    $us = new UserStory(
        id: $id,
        title: 'A correct title',
        description: 'Correct description',
        status: UserStoryStatus::TODO
    );
    $us->assignTo(new Owner('owner-uuid-1', 'alice'));
    return $us;
}

function makeFakeAction(array $existingStories = []): PatchUserStoryAction {
    $repository = new InMemoryUserStoryRepository();
    foreach ($existingStories as $story) {
        $repository->save($story);
    }
    $ownerRepository = new class implements \toubilib\application\ports\spi\OwnerRepository {
        public function findById(string $id): ?\toubilib\domain\entities\Owner { return null; }
    };
    $validator = new CreateUserStoryValidator($ownerRepository);
    $service = new UserStoryService($repository, $ownerRepository, $validator);
    return new PatchUserStoryAction($service);
}

function makeFakePatchRequest(array $body, string $id = 'f47ac10b-58cc-4372-a567-0e02b2c3d479'): array {
    $request = (new ServerRequestFactory())
        ->createServerRequest('PATCH', "/user-stories/$id")
        ->withParsedBody($body);
    return [$request, $id];
}

// ── Tests ─
it('should return 422 if the body is empty', function () {
    $action = makeFakeAction([makeFakeUserStory()]);
    [$rq, $id] = makeFakePatchRequest([]);
    $response = $action($rq, new Response(), ['id' => $id]);
    expect($response->getStatusCode())->toBe(422);
    $body = json_decode((string) $response->getBody(), true);
    expect($body)->toHaveKey('errors');
});

it('should return 422 with errors if the title is invalid', function () {
    $action = makeFakeAction([makeFakeUserStory()]);
    [$rq, $id] = makeFakePatchRequest(['title' => 'AB']);
    $response = $action($rq, new Response(), ['id' => $id]);
    expect($response->getStatusCode())->toBe(422);
    $body = json_decode((string) $response->getBody(), true);
    expect($body['errors'])->toHaveKey('title');
});

it('should return 404 if the user story is not found', function () {
    $action = makeFakeAction([]); 
    [$rq, $id] = makeFakePatchRequest(['title' => 'Correct title']);
    expect(fn() => $action($rq, new Response(), ['id' => $id]))
        ->toThrow(\Slim\Exception\HttpNotFoundException::class);
});

it('should return 200 and update the title', function () {
    $story  = makeFakeUserStory();
    $action = makeFakeAction([$story]);
    [$rq, $id] = makeFakePatchRequest(['title' => 'New correct title']);
    $response = $action($rq, new Response(), ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
    $body = json_decode((string) $response->getBody(), true);
    expect($body['title'])->toBe('New correct title');
});

it('should return 200 and update the description', function () {
    $story  = makeFakeUserStory();
    $action = makeFakeAction([$story]);
    [$rq, $id] = makeFakePatchRequest(['description' => 'New correct description']);
    $response = $action($rq, new Response(), ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
    $body = json_decode((string) $response->getBody(), true);
    expect($body['description'])->toBe('New correct description');
});

it('should return 200 and update both fields', function () {
    $story  = makeFakeUserStory();
    $action = makeFakeAction([$story]);
    [$rq, $id] = makeFakePatchRequest([
        'title' => 'New correct title',
        'description' => 'New correct description',
    ]);
    $response = $action($rq, new Response(), ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
    $body = json_decode((string) $response->getBody(), true);
    expect($body['title'])->toBe('New correct title')
        ->and($body['description'])->toBe('New correct description');
});
