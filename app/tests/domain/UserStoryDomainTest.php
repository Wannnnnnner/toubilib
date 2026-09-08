<?php

use function PHPUnit\Framework\assertEquals;
use jira\domain\entities\UserStory;
use jira\domain\entities\UserStoryStatus;
use jira\domain\exceptions\StatusChangeNotAllowedException;

// ── Tests ─
it("should create a user story with status TODO", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    expect($userStory->getStatus())->toBe(UserStoryStatus::TODO);
});

it("should accept the change of a user story from TODO to WIP", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    $userStory->start();
    expect($userStory->getStatus())->toBe(UserStoryStatus::WIP);
});

it("should accept the change of a user story from WIP to DONE", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    $userStory->start();
    $userStory->finish();
    expect($userStory->getStatus())->toBe(UserStoryStatus::DONE);
});

it("should reject the change of a user story from TODO to DONE", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    expect(fn () => $userStory->finish())
        ->toThrow(StatusChangeNotAllowedException::class, 'Cannot change state from TODO to DONE');
});

it("should reject the change of a user story from TODO to CLOSED", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    expect(fn () => $userStory->close())
        ->toThrow(StatusChangeNotAllowedException::class, 'Cannot change state from TODO to CLOSED');
});

it("should reject the change of a user story from WIP to CLOSED", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    $userStory->start();
    expect(fn () => $userStory->close())
        ->toThrow(StatusChangeNotAllowedException::class, 'Cannot change state from WIP to CLOSED');
});

it("should reject the change of a user story backward from DONE to WIP", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    $userStory->start();
    $userStory->finish();
    expect(fn () => $userStory->start())
        ->toThrow(StatusChangeNotAllowedException::class, 'Cannot change state from DONE to WIP');
});

it("should reject the change of a user story backward from CLOSED to DONE", function() {
    $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $userStory = new UserStory($uuid, "title", "description");
    $userStory->start();
    $userStory->finish();
    $userStory->close();
    expect(fn () => $userStory->finish())
        ->toThrow(StatusChangeNotAllowedException::class, 'Cannot change state from CLOSED to DONE');
});

