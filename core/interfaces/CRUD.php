<?php
namespace core\interfaces;

interface CRUD
{
    public function save(): object|string;
    public function find(array $arr): object|string;
    public function delete(array $arr): array;
    public function update(): array;
    public function findAll(array $arr): array;
}