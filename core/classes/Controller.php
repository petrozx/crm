<?php

namespace core\classes;

use core\traits\Singleton;

#[\Attribute]
abstract class Controller
{
    public array $request;
    public string $method;
    public string $uri;
    public array $headers;

    public function __construct()
    {
        $this->request =  json_decode(file_get_contents('php://input'), true)?:$_POST;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = preg_replace('/\/$/', '', $_SERVER['REDIRECT_URL']);
        $this->headers = getallheaders();
    }

}