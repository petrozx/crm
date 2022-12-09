<?php

namespace core\traits;

use core\classes\EntityManager;
use core\database\DBO;
use core\helpers\Response;
use core\persist\Persist;
use ReflectionClass;
use ReflectionException;

trait Entity
{

    public static function builder(): object
    {
        $args = [];
        $props = get_class_vars(self::class);
        foreach ($props as $_ => $value) {
            if (is_null($value)) {
                $value = '';
            }
            $args[] = $value;
        }
        try {
            return (new ReflectionClass(self::class))->newInstance(...$args);
        } catch (ReflectionException) {
            die("Произошла ошибка в создании экземпяра, без вызова конструктора.");
        }
    }

    public function __call($m, $_)
    {
        try {
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $m, $matches);
            $currentArrayMatches = $matches[0];
            if ($currentArrayMatches[0] === 'find'
                && $currentArrayMatches[1] === 'By') {
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
                $m = $currentArrayMatches[0] . $currentArrayMatches[1];
                $_ = [["=$_v" => $_[0]]];
            }
            return (new EntityManager($this, DBO::getInstance(), $this->prepare()))
                ->$m(
                    !empty($_)
                        ? $_[0]
                        : $_
                );
        } catch (\Exception $e) {
            return $this;
        }
    }

    private function prepare(): array
    {
        $arrRec = [];
        foreach (get_object_vars($this) as $column=>$value) {
            if ($value === null) {
                $value = 'DEFAULT';
            }
            $column = Persist::camelToUnder($column);
            $arrRec[$column] = $value;
        }
        return $arrRec;
    }

}