<?php

namespace core\classes;


use core\helpers\Response;
use core\security\Security;
use core\traits\Singleton;

class Middleware
{
    use Singleton;

    private string $uri;
    private array $request;
    private array $headers;
    private string $requestHttpMethod;


    private function __construct()
    {
        $this->request = $this->getRequest();
        $this->headers = $this->getHeaders();
        $this->requestHttpMethod = $_SERVER['REQUEST_METHOD'];
        $this->uri = preg_replace('/\/$/', '', $_SERVER['REDIRECT_URL']);
    }

    private function getHeaders(): bool|array
    {
        try {
            return getallheaders();
        } catch (\Exception) {
            return [];
        }
    }

    private function getRequest()
    {
        $jDada = file_get_contents('php://input');
        $pData = $_POST;
        return json_decode($jDada, true)?:$pData;
    }

    public function getResponse(): Response
    {
        $security = Security::getInstance()->check($this->request, $this->headers, $this->requestHttpMethod, $this->uri);
    }

}

