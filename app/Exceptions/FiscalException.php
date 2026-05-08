<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class FiscalException extends RuntimeException
{
    private string $errorCode;

    /**
     * @var array<string, mixed>
     */
    private array $details;

    private int $httpStatus;

    /**
     * @param array<string, mixed> $details
     */
    public function __construct(string $message, string $errorCode = 'FISCAL_ERROR', array $details = [], int $httpStatus = 422)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->details = $details;
        $this->httpStatus = $httpStatus;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function details(): array
    {
        return $this->details;
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }
}
