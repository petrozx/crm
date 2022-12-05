<?php
namespace core\classes;

class Env
{
    private string $patch;

    private function __construct($patch)
    {
        $this->patch = $patch;
    }

    public static function link($patch=''): void
    {
        static $instances;
        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass($patch);
        }
        $instances[$calledClass]->readUserEnv();
    }

    private function readUserEnv(): void
    {
        foreach(file("$this->patch.env", FILE_IGNORE_NEW_LINES) as $str) {
            putenv($str);
        }
    }
}