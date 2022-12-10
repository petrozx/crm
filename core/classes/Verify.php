<?php

namespace core\classes;

use core\helpers\Response;
use core\security\TokenConfigure;

class Verify
{
    public static function verify(mixed $request, array $headers, int $duration, string $url): Response
    {
        if (array_key_exists('JWT', $headers)) {
            $data = TokenConfigure::decode($headers['JWT'], getenv('SECRET_KEY'));
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