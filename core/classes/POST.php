<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;
use core\traits\Entity;

#[\Attribute]
class POST extends Controller
{
    public function __construct(
        private string  $point,
        private mixed   $entity = null,
        private ?string $access = null,
    )
    {}

    public function getAccess($userController, $methodController): Response
    {
        if ($this->point === $this->uri) {
            if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
                return Response::take(false, 'Доступ к методу запрещен.');
            }
            if (class_exists($this->entity) && !empty($this->request)) {
                $request = (new Persist($this->entity::builder()))->build($this->request);
            }
            $methodName = $methodController->getName();
            $result = ($userController->newInstance())->$methodName($this->request);
            return Response::take(true, $result);
        } else {
            return Response::take(false, 'Запрашиваемого метода, не существует');
        }
    }

}