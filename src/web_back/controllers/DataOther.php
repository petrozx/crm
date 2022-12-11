<?php

namespace src\web_back\controllers;

use core\classes\Controller;
use core\classes\GET;
use core\classes\POST;
use src\web_back\entity\Users;

#[Controller]
class DataOther
{

    #[GET(point: '/api/v1/{id}')]
    public function hi($id)
    {
        return (Users::builder()->findById($id))->getUserName();
    }
}