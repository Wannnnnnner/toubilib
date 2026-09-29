<?php
namespace toubilib\application\exceptions;

class ValidationException extends \DomainException
{
    private array $errors;

    public function __construct(string $message = 'Validation failed')
    {
        parent::__construct($message);

    }
}