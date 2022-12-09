<?php

namespace core\helpers;

use core\traits\Entity;

class Response
{
    private function __construct(
        public bool $status = true,
        public mixed $body = '',
    ) {}

    public static function take(bool $status, mixed $body): Response
    {
        return new self($status, $body);
    }

    public function toJson(): void
    {
        echo json_encode(['data' => $this]);
    }
}