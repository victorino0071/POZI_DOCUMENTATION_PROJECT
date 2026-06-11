<?php


namespace Core\Http;



class Request {

    public function getUri():string{
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return parse_url($uri, PHP_URL_PATH);
    }

    public function getMethod():string{
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return strtoupper($method);
    }

    public function all():array{
        return $_REQUEST;
    }

    public function input(string $key, mixed $default = null):mixed{
        return $_REQUEST[$key] ?? $default;
    }
}