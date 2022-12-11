<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;

#[\Attribute]
abstract class Controller
{
    public string $uri;
    public array $request;
    public array $method;
    public array $headers;
    public function __construct()
    {
        $this->uri = preg_replace('/\/$/', '', $_SERVER['REDIRECT_URL']);
        $this->request = $this->getRequest();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();;
    }

    private function getRequest()
    {
        $jDada = file_get_contents('php://input');
        $pData = $_POST;
        return json_decode($jDada, true)?:$pData;
    }

//    public function getDataEndpoint(
//        array $controllers,
//        array $JWT,
//        array $request,
//        string $method,
//        string $uri,
//    ): Response
//    {
//        foreach ($controllers as $controllerTuple) {
//            [$userController, $_] = $controllerTuple;
//            $methodsController = $userController->getMethods();
//            foreach ($methodsController as $methodController) {
//                [$methodControllerAttribute] = $methodController->getAttributes();
//                $targetMethod =
//                    preg_replace('/^.+\\\\/m','', $methodControllerAttribute->getName());
//                if($targetMethod === $method) {
//                    return ($methodControllerAttribute->newInstance())->getAccess(
//                        $JWT,
//                        $request,
//                        $uri,
//                        $methodController,
//                        $userController,
//                    );
//                }
//            }
//        }
//        return Response::take(false, 'Запрашиваемый ресурс отсутствует');
//    }
}