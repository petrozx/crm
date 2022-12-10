<?php

namespace core\security;

class SecurityConfig implements Security
{
    function __construct()
    {
        foreach (get_declared_classes() as $className) {
            if (in_array('Iterator', class_implements($className))) {
                echo $className, PHP_EOL;
            }
        }

    }
}