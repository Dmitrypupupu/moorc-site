<?php

namespace App;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = $this->normalize($uri ?: '/');

        header('X-Powered-By: MOORC');

        if (isset($this->routes[$method][$path])) {
            echo call_user_func($this->routes[$method][$path], $_REQUEST);
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function normalize(string $path): string
    {
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}