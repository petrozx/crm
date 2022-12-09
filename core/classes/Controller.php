<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;

#[\Attribute]
class Controller
{

   public function __construct() {}

    public function getDataEndpoint(
        array $controllers,
        array $JWT,
        array $request,
        string $method,
        string $uri,
    ): Response
    {
        foreach ($controllers as $controllerTuple) {
            [$userController, $_] = $controllerTuple;
            $methodsController = $userController->getMethods();
            foreach ($methodsController as $methodController) {
                [$methodControllerAttribute] = $methodController->getAttributes();
                $targetMethod =
                    preg_replace('/^.+\\\\/m','', $methodControllerAttribute->getName());
                if($targetMethod === $method) {
                    return ($methodControllerAttribute->newInstance())->getAccess(
                        $JWT,
                        $request,
                        $uri,
                        $methodController,
                        $userController,
                    );
                }
            }
        }
        return Response::take(false, 'Запрашиваемый ресурс отсутствует');
    }
}