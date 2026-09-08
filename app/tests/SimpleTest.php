<?php
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertEquals;

it("should work", function() {
    expect(true)->toBeTrue();
});

it("should also work", function() {
    expect(false)->toBeFalse();
});

