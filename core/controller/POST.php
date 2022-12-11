<?php

namespace core\controller;

use core\helpers\Response;
use core\persist\Persist;

#[\Attribute]
class POST extends Controller
{
    public function __construct(
        private string  $point,
        private mixed   $entity = null,
        private ?string $access = null,
    )
    {}

    public function post($userController, $methodController): ?Response
    {
        if ($this->point === self::$uri) {
            if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
                return Response::take(false, 'Доступ к методу запрещен.');
            }
            if (class_exists($this->entity) && !empty($this->request)) {
                self::$request = (new Persist($this->entity::builder()))->build(self::$request);
            }
            $methodName = $methodController->getName();
            $result = ($userController->newInstance())->$methodName(self::$request);
            return Response::take(true, $result);
        } else {
            return null;
        }
    }

}