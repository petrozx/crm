<?php

namespace core\classes;

use core\helpers\Response;
use core\persist\Persist;

#[\Attribute]
class GET extends Controller
{
    function __construct(
        private string  $point,
        private mixed   $entity = null,
        private ?string $access = null,
    )
    {}

    public function getAccess($userController, $methodController): Response
    {
        $request = null;
        if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
            return Response::take(false, 'Доступ к методу запрещен.');
        }
        $getParam = preg_replace('/^.+\//m', '', $this->point);
        if (!empty($getParam)) {
            $trueTargetUri = preg_replace('/\/{.+$/m', '', $this->point);
            $trueCurrentUri = preg_replace('~/[a-zA-Z0-9]+$~ui', '', $this->uri);
            if ($trueTargetUri !== $trueCurrentUri) {
                return Response::take(false, 'Запрашиваемого метода, не существует');
            } else {
                $request = preg_replace('/^.+\//m', '', $this->uri);
            }
        }
        $methodName = $methodController->getName();
        $result = ($userController->newInstance())->$methodName($request);
        return Response::take(true, $result);
    }

}