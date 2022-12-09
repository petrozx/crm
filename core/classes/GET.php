<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;

#[\Attribute]
class GET
{
    function __construct(
        private string $uri,
        private mixed $entity = null,
        private string $access = '',
    )
    {}

    public function getAccess($JWT,
                              $request,
                              $uri,
                              $methodController,
                              $userController
    ): Response
    {
        if ($this->access && $this->access !== $JWT['role']) {
            return Response::take(false, 'Доступ к методу запрещен.');
        }
        $getParam = preg_replace('/^.+\//m', '', $this->uri);
        if (!empty($getParam)) {
            $trueTargetUri = preg_replace('/\/{.+$/m', '', $this->uri);
            $trueCurrentUri = preg_replace('~/[a-zA-Z0-9]+$~ui', '', $uri);
            if ($trueTargetUri !== $trueCurrentUri) {
                return Response::take(false, 'Запрашиваемого метода, не существует');
            } else {
                $request = preg_replace('/^.+\//m', '', $uri);
            }
        }
        $methodName = $methodController->getName();
        $result = ($userController->newInstance())->$methodName($request);
        return Response::take(true, $result);
    }

}