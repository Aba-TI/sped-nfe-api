<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\FiscalException;

final class Router
{
    /**
     * @var array<int, array{method:string, pattern:string, handler:callable}>
     */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => rtrim($pattern, '/') ?: '/',
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): JsonResponse
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $attributes = $this->match($route['pattern'], $request->path());
            if ($attributes === null) {
                continue;
            }

            $response = ($route['handler'])($request->withAttributes($attributes));
            if (!$response instanceof JsonResponse) {
                throw new FiscalException(
                    'O controller retornou uma resposta inválida.',
                    'INVALID_RESPONSE',
                    [],
                    500
                );
            }

            return $response;
        }

        throw new FiscalException(
            'Rota não encontrada.',
            'ROUTE_NOT_FOUND',
            [
                'method' => $request->method(),
                'path' => $request->path(),
            ],
            404
        );
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));

        if ($pattern === '/' && $path === '/') {
            return [];
        }

        if (count($patternParts) !== count($pathParts)) {
            return null;
        }

        $attributes = [];

        foreach ($patternParts as $index => $part) {
            $candidate = $pathParts[$index] ?? '';
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $part, $matches) === 1) {
                $attributes[$matches[1]] = $candidate;
                continue;
            }

            if ($part !== $candidate) {
                return null;
            }
        }

        return $attributes;
    }
}
