<?php

namespace traits;

use core\classes\EntityManager;
use core\database\DBO;
use ReflectionClass;
use ReflectionException;

trait Entity
{

    public static function builder(): object
    {
        try {
            return (new ReflectionClass(self::class))->newInstanceWithoutConstructor();
        } catch (ReflectionException) {
            die("Произошла ошибка в создании экземпяра, без вызова конструктора.");
        }
    }

    public function __call($m, $_)
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/',$m,$matches);
        $currentArrayMatches = $matches[0];
        if ($currentArrayMatches[0] === 'find'
            && $currentArrayMatches[1] === 'By'){
                $_v = strtolower(implode("_",
                    array_slice($currentArrayMatches,
                        2, count($currentArrayMatches))));
                $m = $currentArrayMatches[0];
                $_ = [["=$_v" => $_[0]]];
        } else if ($currentArrayMatches[0] === 'find'
            && $currentArrayMatches[1] === 'All'
            && $currentArrayMatches[2] === 'By') {
                $_v = strtolower(implode("_",
                    array_slice($currentArrayMatches,
                        3, count($currentArrayMatches))));
                $m = $currentArrayMatches[0].$currentArrayMatches[1];
                $_ = [["=$_v" => $_[0]]];
        }

        return (new EntityManager($this, DBO::getInstance()))
                ->$m(
                    !empty($_)
                        ? $_[0]
                        : $_
                );

    }

}