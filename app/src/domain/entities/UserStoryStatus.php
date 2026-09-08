<?php
namespace jira\domain\entities;

enum UserStoryStatus: string {
    case TODO = 'TODO';
    case WIP = 'WIP';
    case DONE = 'DONE';
    case CLOSED = 'CLOSED';

    public static function fromString(string $status): ?self {
        return match ($status) {
            'TODO' => self::TODO,
            'WIP' => self::WIP,
            'DONE' => self::DONE,
            'CLOSED' => self::CLOSED,
            default => null,
        };
    }
}