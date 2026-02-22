<?php

use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        Database::setConnection($pdo);
        Database::updateQuery('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, age INTEGER NOT NULL)');
    }

    protected function tearDown(): void
    {
        Database::resetConnection();
    }

    public function testInsertAndRead(): void
    {
        $ok = Database::updateQuery('INSERT INTO users (name, age) VALUES (?, ?)', 'alice', 30);
        $this->assertTrue($ok);

        $stmt = Database::query('SELECT name, age FROM users WHERE id = ?', 1);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('alice', $row['name']);
        $this->assertSame(30, (int) $row['age']);
    }

    public function testUpdateAndDelete(): void
    {
        Database::updateQuery('INSERT INTO users (name, age) VALUES (?, ?)', 'bob', 25);

        $updated = Database::updateQuery('UPDATE users SET age = ? WHERE name = ?', 26, 'bob');
        $this->assertTrue($updated);

        $stmt = Database::query('SELECT age FROM users WHERE name = ?', 'bob');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertSame(26, (int) $row['age']);

        $deleted = Database::updateQuery('DELETE FROM users WHERE name = ?', 'bob');
        $this->assertTrue($deleted);

        $stmt = Database::query('SELECT COUNT(*) AS c FROM users WHERE name = ?', 'bob');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertSame(0, (int) $row['c']);
    }
}
