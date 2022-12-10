<?php

namespace core\classes;

use core\helpers\Response;
use core\security\TokenConfigure;

#[\Attribute]
class Login
{

    public function __construct(
        private readonly string $url,
        private readonly mixed $entity,
        private readonly string $access,
    )
    {}

    public function login(mixed $request, array $headers, int $duration, string $url): ?Response
    {
        if ($this->url === $url) {
            if ($this->access) {
                $verify = self::verify($headers);
                if (!$verify->status || $this->access !== $verify->body['role']) {
                    return Response::take(false, 'У вас нет прав на эту операцию.');
                }
            }
            try {
                $user = $this->entity::builder()->findByUserName($request['username']);
                if (password_verify($request['password'], $user->getPassword())) {
                    $_SESSION['AUTH'] = $user->getUserName();
                    return Response::take(
                        true,
                        TokenConfigure::encode(
                            username: $user->getUserName(),
                            currentTime: time(),
                            expireTime: time() + $duration,
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
        } else {
            return null;
        }
    }

}