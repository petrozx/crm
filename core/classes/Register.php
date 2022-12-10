<?php

namespace core\classes;

use core\helpers\Response;
use core\security\TokenConfigure;

#[\Attribute]
class Register
{

    public function __construct(
        private readonly string $uri,
        private readonly mixed $entity,
        private readonly string $access,
    ){}

    public function register(mixed $request, array $headers, int $duration, string $uri): ?Response
    {
        if ($this->uri === $uri) {
            if ($this->access) {
                $verify = self::verify($headers);
                if (!$verify->status || $this->access !== $verify->body['role']) {
                    return Response::take(false, 'У вас нет прав на эту операцию.');
                }
            }
            try {
                $user = (new $this->entity(
                    $request['username'],
                    password_hash($request['password'], PASSWORD_DEFAULT),
                    $request['role']
                ))->save();
                return Response::take(true,
                    TokenConfigure::encode(
                        username: $user->getUserName(),
                        currentTime: time(),
                        expireTime: time() + $duration,
                        secretKey: getenv('SECRET_KEY'),
                        role: $request['role'],
                    )
                );
            } catch (\Exception) {
                return Response::take(false, 'При сохранении что то пошло нет так, 
            попробуйте другие данные.');
            }
        } else {
            return null;
        }
    }
}