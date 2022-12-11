<?php

namespace core\security;

use core\controller\Controller;
use core\helpers\Response;

#[\Attribute]
class Login extends Controller
{
    public function __construct(private string  $point,
                                private mixed   $entity = null,
                                private ?string $access = null,
    ){}

    public function login(): ?Response
    {
        if (self::$uri === $this->point) {
            if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
                return Response::take(false, 'У вас нет прав, на эту операцию.');
            }
            try {
                $user = $this->entity::builder()->findByUserName(self::$request['username']);
                if (password_verify(self::$request['password'], $user->getPassword())) {
                    $_SESSION['AUTH']['NAME'] = $user->getUserName();
                    $_SESSION['AUTH']['ROLE'] = $user->getRole();
                    return Response::take(
                        true,
                        TokenConfigure::encode()
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