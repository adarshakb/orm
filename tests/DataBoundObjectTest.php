<?php

use PHPUnit\Framework\TestCase;

final class DataBoundObjectTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        Database::setConnection($pdo);
        Database::updateQuery('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, email TEXT NOT NULL)');
    }

    protected function tearDown(): void
    {
        Database::resetConnection();
    }

    public function testInsertLoadSaveFlow(): void
    {
        $u = new TestUser();
        $u->setname('alice');
        $u->setemail('alice@example.com');
        $u->insert();

        $loaded = new TestUser(array(1));
        $this->assertSame('alice', $loaded->getname());

        $loaded->setname('alice2');
        $loaded->save();

        $again = new TestUser(array(1));
        $this->assertSame('alice2', $again->getname());
    }

    public function testDeleteOnDestructWhenMarked(): void
    {
        $u = new TestUser();
        $u->setname('bob');
        $u->setemail('bob@example.com');
        $u->insert();

        $loaded = new TestUser(array(1));
        $loaded->markForDeletion();
        unset($loaded);

        $stmt = Database::query('SELECT COUNT(*) as c FROM users');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertSame(0, (int)$row['c']);
    }
}

class TestUser extends DataBoundObject
{
    protected $id;
    protected $name;
    protected $email;

    protected function DefineTableName() { return 'users'; }
    protected function DefineRelationMap() { return array('id' => 'id', 'name' => 'name', 'email' => 'email'); }
    protected function DefineID() { return array('id'); }
    protected function DefineAutoIncrementField() { return 'id'; }
}
