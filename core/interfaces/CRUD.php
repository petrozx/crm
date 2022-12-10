<?php
namespace core\interfaces;

interface CRUD
{
    public function save(): object|string;
    public function find(array $arr): object|string;
    public function delete(array $arr): bool;
    public function update(): bool;
    public function findAll(array $arr): array;
}