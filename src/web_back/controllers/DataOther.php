<?php

namespace src\web_back\controllers;

use core\controller\GET;
use core\controller\PointRouter;
use src\web_back\entity\Users;

#[PointRouter]
class DataOther
{

    #[GET(point: '/api/v1/{id}')]
    public function hi($id)
    {
        return (Users::builder()->findById($id))->getUserName();
    }

    #[GET(point: '/git/answer')]
    public function hihi()
    {
        return 'hi';
    }
}