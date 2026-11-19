<?php

use App\DAO\DAOFactory;
use App\DAO\NoteDAO;
use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->bind('Core\Database', function () {
    $config = require base_path('config.php');

    return new Database($config['database']);
});

$container->bind(NoteDAO::class, fn() => DAOFactory::notes());

App::setContainer($container);
