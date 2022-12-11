<?php

namespace core\controller;

use core\helpers\Response;

#[\Attribute]
class PointRouter extends Controller
{
    public function checkPoint($controllersUserClasses): Response
    {
        if (!empty($controllersUserClasses)) {
            foreach ($controllersUserClasses as [$controllersUserClass, $_]) {
                foreach ($controllersUserClass->getMethods() as $controllersUserMethod) {
                    foreach ($controllersUserMethod->getAttributes() as $controllersUserMethodAttribute) {
                        $controllersUserMethodAttributeShortName =
                            preg_replace('/^.+\\\\/m', '', $controllersUserMethodAttribute->getName());
                        if (self::$method === $controllersUserMethodAttributeShortName) {
                            $methodName = strtolower(preg_replace('/^.+\\\\/m', '', $controllersUserMethodAttribute->getName()));
                            $res = $controllersUserMethodAttribute->newInstance()->$methodName($controllersUserClass, $controllersUserMethod);
                            if (!is_null($res)) return $res;
                        }
                    }
                }
            }
        } else {
            return Response::take(false, 'Отсутствуют, классы контроллера');
        }
        return Response::take(false, 'Запрашиваемый метод отсутствует');
    }
}