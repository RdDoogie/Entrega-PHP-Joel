<?php

namespace App\DAO;

interface NoteDAO
{
    public function findByUser(int $userId): array;

    public function find(int $id): array|false;

    public function create(string $body, int $userId): void;

    public function update(int $id, string $body): void;

    public function delete(int $id): void;
}
