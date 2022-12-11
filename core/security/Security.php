<?php

namespace core\security;

use core\controller\Controller;
use core\helpers\Response;

#[\Attribute]
class Security extends Controller
{
    public function checkSecurity($securityUserConfig)
    {
        if (!empty($securityUserConfig)) {
            foreach ($securityUserConfig->getMethods() as $securityReflectionMethod) {
                [$securityReflectionMethodAttributes[]] = $securityReflectionMethod->getAttributes();
            }
            return $this->checkMethod($securityReflectionMethodAttributes);
        } else {
            return Response::take(false, "Отсутствует, либо не корректно настроен класс авторизации");
        }
    }

    private function checkMethod(mixed $reflectionMethodAttributes, $position = 'current')
    {
        if ($reflectionMethodAttribute = $position($reflectionMethodAttributes)) {
            $methodName = strtolower(preg_replace('/^.+\\\\/m', '', $reflectionMethodAttribute->getName()));
            $callMethod = $reflectionMethodAttribute->newInstance()->$methodName();
            return match (is_null($callMethod)) {
                false => $callMethod,
                true => $this->checkMethod($reflectionMethodAttributes, 'next'),
            };
        } else {
            return Response::take(false, 'Вы не авторизованы');
        }
    }
}