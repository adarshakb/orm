<?php

require_once __DIR__ . '/../../DataBoundObject.php';

class User extends DataBoundObject
{
    protected $id;
    protected $name;
    protected $email;

    protected function DefineTableName()
    {
        return 'users';
    }

    protected function DefineRelationMap()
    {
        return array(
            'id' => 'id',
            'name' => 'name',
            'email' => 'email'
        );
    }

    protected function DefineID()
    {
        return array('id');
    }

    protected function DefineAutoIncrementField()
    {
        return 'id';
    }
}
