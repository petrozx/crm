<?php

namespace core\security;

use core\classes\Controller;
use core\helpers\Response;
use core\traits\Singleton;

class Verify extends Controller
{
    use Singleton;
    public function verify(): Response
    {
        if (array_key_exists('JWT', $this->headers)) {
            $data = TokenConfigure::decode($this->headers['JWT']);
            if ($data) {
                if ($data['timeEnd'] > time() && $_SESSION['AUTH'] === $data['username']) {
                    return Response::take(true, $data);
                } else {
                    return Response::take(false, 'Ваша сессия истекла, пожалуйста авторизуйтесь снова.');
                }
            } else {
                return Response::take(false, 'Токен не валидный.');
            }
        } else {
            return Response::take(false, 'Вы не авторизованы.');
        }
    }
}