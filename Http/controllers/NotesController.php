<?php

namespace Http\controllers;

use App\DAO\MySqlNoteDAO;
use Core\App;
use App\DAO\NoteDAO;
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

        $notes = App::resolve(NoteDAO::class)->findByUser($this->userId());

        view("notes/index.view.php", [
            'heading' => 'My Notes',
            'notes' => $notes
        ]);
    }

    public function show()
    {

        $note = App::resolve(NoteDAO::class)->find((int) $_GET['id']) ?? abort();

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

        App::resolve(NoteDAO::class)->create($_POST['body'], $this->userId());

        header('location: /notes');
        die();
    }

    public function edit()
    {
        $note = App::resolve(NoteDAO::class)->find((int) $_GET['id']) ?? abort();

        authorize((int) $note['user_id'] === $this->userId());

        view("notes/edit.view.php", [
            'heading' => 'Edit Note',
            'errors' => [],
            'note' => $note
        ]);
    }

    public function update()
    {
        $note = App::resolve(NoteDAO::class)->find((int) $_POST['id']) ?? abort();

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
        App::resolve(NoteDAO::class)->update($note['id'], $_POST['body']);

        header('location: /notes');
        die();
    }

    public function destroy()
    {
        $note = App::resolve(NoteDAO::class)->find((int) $_POST['id']) ?? abort();


        authorize((int) $note['user_id'] === $this->userId());

        App::resolve(NoteDAO::class)->delete($note['id']);

        header('location: /notes');
        exit();
    }
}
