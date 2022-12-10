<?php

namespace src\web_back;

use core\security\Security;
use src\web_back\entity\Users;
use core\classes\Login;
use core\classes\Register;

class SecurityURI implements Security
{
    #[Login(uri: '/user/login', entity: Users::class)]
    function login(){}

    #[register(uri: '/user/register', entity: Users::class)]
    function register(){}
}