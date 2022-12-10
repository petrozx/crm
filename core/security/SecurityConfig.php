<?php

namespace core\security;

class SecurityConfig implements Security
{
    function __construct()
    {
        var_dump(class_implements(class_implements($this)['core\security\Security']));
    }
}