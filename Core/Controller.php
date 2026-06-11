<?php

namespace Core;


abstract class Controller{


    protected function json(mixed $data, int $status = 200): void{
        http_response_code($status);


        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void{
        header("Location: {$url}");
        exit;
    }
}