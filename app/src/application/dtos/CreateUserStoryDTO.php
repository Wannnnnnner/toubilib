<?php
namespace jira\application\dtos;

use jira\application\exceptions\ValidationException;

final class CreateUserStoryDTO {

    private function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $ownerId
    ) {}

    public static function fromArray(array $body): self {
        $title = trim(self::asString($body['title'] ?? ''));
        $description = trim(self::asString($body['description'] ?? ''));
        $ownerId = trim(self::asString($body['ownerId'] ?? ''));

        $errors = [];
        if (strlen($title) < 3 || strlen($title) > 255) {
            $errors['title'] = 'Title must be between 3 and 255 characters long.';
        }
        if (strlen($description) < 10) {
            $errors['description'] = 'Description must be at least 10 characters long.';
        }
        if ($ownerId === '') {
            $errors['ownerId'] = 'OwnerId is mandatory.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        return new self($title, $description, $ownerId);
    }

    private static function asString(mixed $value): string {
        if (!is_string($value) && !is_numeric($value)) {
            throw new ValidationException(['body' => 'Invalid field type in request body.']);
        }
        return (string) $value;
    }
}
