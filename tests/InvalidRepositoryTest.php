<?php

namespace Guardian360\Repository\Tests;

use Illuminate\Container\Container as App;
use Guardian360\Repository\Exceptions\RepositoryException;
use Guardian360\Repository\AbstractRepository as Repository;

class InvalidRepositoryTest extends TestCase
{
    /** @test */
    public function itThrowsAnErrorWhenAInvalidModelIsUsed()
    {
        $this->expectException(RepositoryException::class);

        new ModelRepository(new App);
    }
}

class ModelRepository extends Repository
{
    /**
     * Specify the model's class name.
     *
     * @return string
     */
    public function model()
    {
        return Model::class;
    }
}

class Model
{
    //
}
