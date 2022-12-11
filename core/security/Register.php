<?php

namespace core\security;

use core\controller\Controller;
use core\helpers\Response;

#[\Attribute]
class Register extends Controller
{

    public function __construct(private string  $point,
                                private mixed   $entity = null,
                                private ?string $access = null,
    ){}

    public function register(): ?Response
    {
        if (self::$uri === $this->point) {
            if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
                return Response::take(false, 'У вас нет прав, на эту операцию.');
            }
            try {
                (new $this->entity(
                    self::$request['username'],
                    password_hash(self::$request['password'], PASSWORD_DEFAULT),
                    self::$request['role']
                ))->save();
                return Response::take(true,
                    TokenConfigure::encode()
                );
            } catch (\Exception) {
                return Response::take(false, 'При сохранении что то пошло нет так, попробуйте другие данные.');
            }
        } else {
            return null;
        }
    }
}