<?php

namespace src\web_back\controllers;

#[Controller]
class UserController
{

    #[GET('get/hello')]
    public function getHello()
    {
        echo 'hello';
    }

}