<?php

namespace App\DAO;

use Core\App;
use Core\Database;

class DAOFactory
{
    public static function notes(): NoteDAO
    {
        return new MySqlNoteDAO(App::resolve(Database::class));
    }
}
