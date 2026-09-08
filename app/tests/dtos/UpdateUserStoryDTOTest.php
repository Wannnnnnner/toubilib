<?php

use jira\application\dtos\UpdateUserStoryDTO;
use jira\application\exceptions\ValidationException;

// ── Tests ─
it('should trim the provided values', function () {
    $dto = UpdateUserStoryDTO::fromArray(['title' => '  The title  ']);
    expect($dto->title)->toBe('The title')
        ->and($dto->description)->toBeNull();
});

it('should accept a complete body', function () {
    $dto = UpdateUserStoryDTO::fromArray([
        'title' => 'The title',
        'description' => 'The long description',
    ]);
    expect($dto->title)->toBe('The title')
        ->and($dto->description)->toBe('The long description');
});

it('should throw ValidationException if body is empty', function () {
    UpdateUserStoryDTO::fromArray([]);
})->throws(ValidationException::class);
