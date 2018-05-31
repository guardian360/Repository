<?php

namespace Guardian360\Repository\Tests;

use Guardian360\Repository\Tests\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /**
     * @return void
     */
    protected function setUpDatabase()
    {
        $database = new DB;

        $database->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $database->bootEloquent();
        $database->setAsGlobal();
    }

    /**
     * @return void
     */
    protected function migrateTables()
    {
        DB::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('role');
            $table->timestamps();
        });

        foreach ($this->users() as $attributes) {
            User::create($attributes);
        }
    }

    /**
     * @return array
     */
    protected function users()
    {
        return [
            ['name' => 'Joe', 'role' => 'user'],
            ['name' => 'Jane', 'role' => 'user'],
            ['name' => 'Hans', 'role' => 'user'],
            ['name' => 'Rupert', 'role' => 'user'],
            ['name' => 'Elise', 'role' => 'admin'],
            ['name' => 'George', 'role' => 'admin'],
        ];
    }
}
