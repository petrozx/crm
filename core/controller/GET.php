<?php

namespace core\controller;

use core\helpers\Response;

#[\Attribute]
class GET extends Controller
{
    function __construct(
        private string  $point,
        private mixed   $entity = null,
        private ?string $access = null,
    )
    {}

    public function get($userController, $methodController): ?Response
    {
        $request = null;
        if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
            return Response::take(false, 'Доступ к методу запрещен.');
        }
        if($this->point !== self::$uri) {
            $getParam = preg_replace('/^.+\/[0-9a-zA-Z]+/m', '', $this->point);
            if (!empty($getParam)) {
                $trueTargetUri = preg_replace('/\/{.+$/m', '', $this->point);
                $trueCurrentUri = preg_replace('~/[a-zA-Z0-9]+$~ui', '', self::$uri);
                if ($trueTargetUri !== $trueCurrentUri) {
                    return null;
                } else {
                    $request = preg_replace('/^.+\//m', '', self::$uri);
                }
            } else {
                return null;
            }
        }
        $methodName = $methodController->getName();
        $result = ($userController->newInstance())->$methodName($request);
        return Response::take(true, $result);

    }

}