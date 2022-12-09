<?php

namespace src\web_back;

use core\security\Security;
use src\web_back\entity\Users;

#[Security]
interface SecurityURI
{
    #[login(uri: '/user/login', entity: Users::class)]
    function login();

    #[register(uri: '/user/register', entity: Users::class, access: 'ADMIN')]
    function register();
}