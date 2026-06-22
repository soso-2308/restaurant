<?php
namespace App\Exceptions;

class ReservationException extends \Exception
{
    private array $errors = [];

    public function __construct(string $message, int $code = 400, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}