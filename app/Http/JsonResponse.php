<?php

declare(strict_types=1);

namespace App\Http;

final class JsonResponse
{
    private int $status;

    /**
     * @var array<string, mixed>
     */
    private array $payload;

    /**
     * @var array<string, string>
     */
    private array $headers;

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string> $headers
     */
    public function __construct(int $status, array $payload, array $headers = [])
    {
        $this->status = $status;
        $this->payload = $payload;
        $this->headers = $headers;
    }

    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
