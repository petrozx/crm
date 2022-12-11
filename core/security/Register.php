<?php

namespace core\security;

use core\classes\Controller;
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
        if ($this->uri === $this->point) {
            if ($this->access && $this->access !== $_SESSION['AUTH']['ROLE']) {
                return Response::take(false, 'У вас нет прав, на эту операцию.');
            }
            try {
                (new $this->entity(
                    $this->request['username'],
                    password_hash($this->request['password'], PASSWORD_DEFAULT),
                    $this->request['role']
                ))->save();
                return Response::take(true,
                    TokenConfigure::encode()
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