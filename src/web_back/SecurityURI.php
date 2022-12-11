<?php

namespace src\web_back;

use core\security\Login;
use core\security\Register;
use core\security\Security;
use src\web_back\entity\Users;

#[Security]
interface SecurityURI
{
    #[Login(point: '/user/login', entity: Users::class)]
    function login();

    #[Register(point: '/user/register', entity: Users::class, access: 'ADMIN')]
    function register();
}