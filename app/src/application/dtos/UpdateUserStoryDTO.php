<?php
namespace jira\application\dtos;

use jira\application\exceptions\ValidationException;

final class UpdateUserStoryDTO {

    private function __construct(
        public readonly ?string $title,
        public readonly ?string $description,
    ) {}

    public static function fromArray(array $body): self {
        $errors = [];

        $title = null;
        if (array_key_exists('title', $body)) {
            $title = trim(self::asString($body['title'], 'title', $errors));
            if ($title !== null && (strlen($title) < 3 || strlen($title) > 255)) {
                $errors['title'] = 'Title must be between 3 and 255 characters long.';
            }
        }

        $description = null;
        if (array_key_exists('description', $body)) {
            $description = trim(self::asString($body['description'], 'description', $errors));
            if ($description !== null && strlen($description) < 10) {
                $errors['description'] = 'Description must be at least 10 characters long.';
            }
        }

        if ($title === null && $description === null) {
            $errors['body'] = 'At least one field (title or description) must be provided.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return new self($title, $description);
    }

    private static function asString(mixed $value, string $field, array &$errors): ?string {
        if (!is_string($value) && !is_numeric($value)) {
            $errors[$field] = ucfirst($field) . ' must be a string.';
            return null;
        }
        return (string) $value;
    }
}