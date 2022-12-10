<?php

namespace src\web_back\controllers;
use core\classes\Controller;
use core\classes\POST;
use src\web_back\entity\Users;

#[Controller]
class UserController
{

    #[POST(uri: '/get/hello', entity: Users::class)]
    public function getHello(Users $users)
    {
        return $users->save();
    }

    #[POST(uri: '/api/post')]
    public function lalala()
    {
        return "you are in lalala";
    }

}