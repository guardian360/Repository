<?php

namespace Guardian360\Repository\Tests\Repositories;

use Guardian360\Repository\Tests\Models\User;
use Guardian360\Repository\AbstractRepository as Repository;

class UserRepository extends Repository
{
    /**
     * Specify the model's class name.
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }
}
