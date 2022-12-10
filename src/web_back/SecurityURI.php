<?php

namespace src\web_back;

use core\classes\Login;
use core\classes\Register;
use core\security\Security;
use src\web_back\entity\Users;
class SecurityURI implements Security
{
    #[Login(uri: '/user/login', entity: Users::class)]
    function login(){}

    #[Register(uri: '/user/register', entity: Users::class)]
    function register(){}
}