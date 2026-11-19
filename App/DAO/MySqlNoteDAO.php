<?php

namespace App\DAO;

use Core\Database;

class MySqlNoteDAO implements NoteDAO
{
    public function __construct(protected Database $db)
    {
    }

    public function findByUser(int $userId): array
    {
        return $this->db->query('select * from notes where user_id = :user_id', [
            'user_id' => $userId
        ])->get();
    }

    public function find(int $id): array|false
    {
        return $this->db->query('select * from notes where id = :id', [
            'id' => $id
        ])->find();
    }

    public function create(string $body, int $userId): void
    {
        $this->db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body' => $body,
            'user_id' => $userId
        ]);
    }

    public function update(int $id, string $body): void
    {
        $this->db->query('update notes set body = :body where id = :id', [
            'id' => $id,
            'body' => $body
        ]);
    }

    public function delete(int $id): void
    {
        $this->db->query('delete from notes where id = :id', [
            'id' => $id
        ]);
    }
}
