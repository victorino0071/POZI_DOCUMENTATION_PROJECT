<?php
namespace Core\Http;


class Request{

    public function getMethod():string{
        return strtolower($_SERVER['REQUEST_METHOD']??'get');
    }


    public function getUri() : string{
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $position = strpos($uri, '?');

        if($position !== false){
            $uri = substr($uri, 0, $position);
        }

        return $uri;
    }
}