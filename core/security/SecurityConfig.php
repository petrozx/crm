<?php

use core\security\Security;

class SecurityConfig implements Security
{
    public function __construct()
    {
        var_dump('hi');
    }
}