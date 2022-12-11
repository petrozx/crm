<?php

namespace src\web_back\controllers;
use core\controller\PointRouter;
use core\controller\POST;
use src\web_back\entity\Users;

#[PointRouter]
class UserController
{

    #[POST(point: '/get/hello', entity: Users::class)]
    public function getHello()
    {
        return '$users->save()';
    }

    #[POST(point: '/api/post')]
    public function getlala()
    {
        return "you are in lalala";
    }

}