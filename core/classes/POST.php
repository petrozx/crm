<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;
use core\traits\Entity;

#[\Attribute]
class POST
{
    function __construct(
        private string $uri,
        private mixed  $entity = null,
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
        if ($this->uri === $uri) {
            if ($this->access && $this->access !== $JWT['role']) {
                return Response::take(false, 'Доступ к методу запрещен.');
            }
            if (class_exists($this->entity) && !empty($request)) {
                $request = (new Persist($this->entity::builder()))->build($request);
            }
            $methodName = $methodController->getName();
            $result = ($userController->newInstance())->$methodName($request);
            return Response::take(true, $result);
        } else {
            return Response::take(false, 'Запрашиваемого метода, не существует');
        }
    }

}