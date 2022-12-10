<?php

namespace core\classes;


use core\helpers\Response;
use core\security\SecurityConfig;
use core\traits\Singleton;

class Middleware extends Controller
{

    use Singleton;

    private function __construct()
    {
        parent::__construct();
    }
    public function getResponse()
    {
//        $verify = Verify::verify($this->headers);
//        if (!$verify->status) return $verify;
        new SecurityConfig();

    }

}

