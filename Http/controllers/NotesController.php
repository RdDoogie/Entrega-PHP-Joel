<?php

namespace Http\controllers;

use Core\App;
use Core\Database;
use Core\Validator;

class NotesController
{
    protected Database $db;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
    }

    protected function userId(): int
    {
        return (int) $_SESSION['user']['id'];
    }

    public function index()
    {
        $notes = $this->db->query('select * from notes where user_id = :user_id', [
            'user_id' => $this->userId()
        ])->get();

        view("notes/index.view.php", [
            'heading' => 'My Notes',
            'notes' => $notes
        ]);
    }

    public function show()
    {
        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $_GET['id']
        ])->findOrFail();

        authorize((int) $note['user_id'] === $this->userId());

        view("notes/show.view.php", [
            'heading' => 'Note',
            'note' => $note
        ]);
    }

    public function create()
    {
        view("notes/create.view.php", [
            'heading' => 'Create Note',
            'errors' => []
        ]);
    }

    public function store()
    {
        $errors = [];

        if (! Validator::string($_POST['body'], 1, 1000)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

        if (! empty($errors)) {
            return view("notes/create.view.php", [
                'heading' => 'Create Note',
                'errors' => $errors
            ]);
        }

        $this->db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body' => $_POST['body'],
            'user_id' => $this->userId()
        ]);

        header('location: /notes');
        die();
    }

    public function edit()
    {
        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $_GET['id']
        ])->findOrFail();

        authorize((int) $note['user_id'] === $this->userId());

        view("notes/edit.view.php", [
            'heading' => 'Edit Note',
            'errors' => [],
            'note' => $note
        ]);
    }

    public function update()
    {
        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $_POST['id']
        ])->findOrFail();

        authorize((int) $note['user_id'] === $this->userId());

        $errors = [];

        if (! Validator::string($_POST['body'], 1, 1000)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

        if (count($errors)) {
            return view('notes/edit.view.php', [
                'heading' => 'Edit Note',
                'errors' => $errors,
                'note' => $note
            ]);
        }

        $this->db->query('update notes set body = :body where id = :id', [
            'id' => $_POST['id'],
            'body' => $_POST['body']
        ]);

        // redirect the user
        header('location: /notes');
        die();
    }

    public function destroy()
    {
        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $_POST['id']
        ])->findOrFail();

        authorize((int) $note['user_id'] === $this->userId());

        $this->db->query('delete from notes where id = :id', [
            'id' => $_POST['id']
        ]);

        header('location: /notes');
        exit();
    }
}
