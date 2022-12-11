<?php

namespace core\helpers;

use core\traits\Singleton;
use ReflectionClass;

class ScanUserDirectory
{

    use Singleton;

    private const WAY = '../../src/web_back/';

    private function scanClasses($dir = self::WAY): array
    {

        $result = array();

        $cdir = scandir($dir);

        foreach ($cdir as $key => $value)
        {

            if (!in_array($value,array(".","..")))
            {
                $currentWay = str_replace('../..', '', $dir);
                $currentWay = str_replace('/', '\\', $currentWay);
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$currentWay . $value] = $this->scanClasses($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    if ($value[0] === strtoupper($value[0])) {
                        if ($dir === self::WAY) {
                            $result[rtrim($currentWay, '\\')][] = preg_replace('/\.php$/', '', $value);
                        } else
                            $result[] = preg_replace('/\.php$/', '', $value);
                    }
                }
            }
        }

        return $result;
    }

    public function getAllReflectionClasses(): Response
    {
        $_ = [];
        foreach ($this->scanClasses() as $way => $names) {
            foreach ($names as $name) {
                $fullClassName = $way . '\\' . $name;
                try {
                    $reflectionClass = new ReflectionClass($fullClassName);
                    $classAttributes = $reflectionClass->getAttributes();
                    foreach ($classAttributes as $classAttribute) {
                        $_[preg_replace('/^.+\\\\/m', '', $classAttribute->getName())][] =
                            [$reflectionClass, $classAttribute->getName()];
                    }
                } catch (\Exception) {
                    return Response::take(false, 'Ошибка в конфигурационных классах.');
                }
            }
        }
        return Response::take(true, $_);
    }
}