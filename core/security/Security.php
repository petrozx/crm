<?php

namespace core\security;

use core\traits\Singleton;

#[\Attribute]
class Security
{

    use Singleton;
    private const duration = 86400;

    public function check($request, $headers, $requestHttpMethod, $uri)
    {
        $matches = match (null) {
        }
    }

}