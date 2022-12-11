<?php

namespace core\controller;

abstract class Controller
{
    static string $uri;
    static mixed $request;
    static string $method;
    static array $headers = [];
    public function __construct()
    {
        self::$uri = preg_replace('/\/$/', '', $_SERVER['REDIRECT_URL']);
        self::$request = $this->getRequest();
        self::$method = $_SERVER['REQUEST_METHOD'];
        self::$headers = getallheaders();
    }

    private function getRequest()
    {
        $jDada = file_get_contents('php://input');
        $pData = $_POST;
        return json_decode($jDada, true)?:$pData;
    }
}