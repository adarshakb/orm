<?php

require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/User.php';

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
Database::setConnection($pdo);

Database::updateQuery('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL)');

$user = new User();
$user->setname('Ada Lovelace');
$user->setemail('ada@example.com');
$user->insert();

$loaded = new User(array(1));
echo 'Loaded user: ' . $loaded->getname() . ' <' . $loaded->getemail() . '>' . PHP_EOL;
