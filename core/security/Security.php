<?php

namespace core\security;

use core\helpers\Response;

#[\Attribute]
class Security
{

    private const duration = 86400;

    public static function login(mixed $request, mixed $entity, mixed $role): Response
    {
        try {
            $user = $entity::builder()->findByUserName($request['username']);
            if (password_verify($request['password'], $user->getPassword())) {
                $_SESSION['AUTH'] = $user->getUserName();
                return Response::take(
                    true,
                    TokenConfigure::encode(
                        username: $user->getUserName(),
                        currentTime: time(),
                        expireTime: time()+self::duration,
                        secretKey: getenv('SECRET_KEY'),
                        role: $user->getRole(),
                    )
                );
            } else {
                return Response::take(false, 'Не верный логин или пароль.');
            }
        } catch (\Exception $e) {
            return Response::take(false, $e->getMessage());
        }
    }

    public static function register(mixed $request, mixed $entity, mixed $role, array $headers): Response
    {
        if ($role) {
            $verify = self::verify($headers);
            if (!$verify->status || $role !== $verify->body['role']) {
                return Response::take(false, 'У вас нет прав на эту операцию.');
            }
        }
        try {
            $user = (new $entity(
                $request['username'],
                password_hash($request['password'], PASSWORD_DEFAULT),
                $request['role']
            ))->save();
            return Response::take(true,
                TokenConfigure::encode(
                    username: $user->getUserName(),
                    currentTime: time(),
                    expireTime: time()+self::duration,
                    secretKey: getenv('SECRET_KEY'),
                    role: $request['role'],
                )
            );
        } catch (\Exception) {
            return Response::take(false, 'При сохранении что то пошло нет так, 
            попробуйте другие данные.');
        }
    }

    public static function verify(array $headers): Response
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