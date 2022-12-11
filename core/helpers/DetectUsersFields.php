<?php

namespace core\helpers;

class DetectUsersFields
{
    public static function detect($entity): Response
    {
        $res = null;
        try {
            foreach ((new \ReflectionClass($entity))->getProperties() as $prop) {
                foreach ($prop->getAttributes() as $attribute) {
                    $res[preg_replace('/^.+\\\\/m', '', $attribute->getName())] = ucfirst($prop->getName());
                }
            }
        } catch (\ReflectionException $e) {
            return Response::take(false, 'Ошибка в конфигурации сущности, для пользователей');
        }
        return Response::take(true, $res);
    }
}