<?php

namespace Guardian360\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Guardian360\Repository\Exceptions\RepositoryException;
use Guardian360\Repository\Contracts\RepositoryContract as Repository;
use Guardian360\Repository\Contracts\SpecificationContract as Specification;
use Guardian360\Repository\Contracts\RepositorySpecificationContract as RepositorySpecification;

abstract class AbstractRepository implements Repository, RepositorySpecification
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $specifications = [];

    /**
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->makeModel();
    }

    /**
     * Specify the model's class name.
     *
     * @return string
     */
    abstract public function model();

    /**
     * @throws \Guardian360\Repository\Exceptions\RepositoryException
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model()} must be an instance of \\Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return $this->model = $model;
    }

    /**
     * @param  array  $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * @return []\Illuminate\Database\Eloquent\Model
     */
    public function all(array $columns = ['*'])
    {
        $this->applySpecs();

        return $this->model->get($columns);
    }

    /**
     * @param  mixed  $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id)
    {
        $this->applySpecs();

        return $this->model->find($id);
    }

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findBy(string $attribute, $value, array $columns = ['*'])
    {
        $this->applySpecs();

        return $this->model->where($attribute, $value)->first($columns);
    }

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @return []\Illuminate\Database\Eloquent\Model
     */
    public function findAllBy(string $attribute, $value, array $columns = ['*'])
    {
        $this->applySpecs();

        if (is_array($value)) {
            return $this->model->whereIn($attribute, $value)->get($columns);
        }

        return $this->model->where($attribute, $value)->get($columns);
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param  array   $data
     * @param  mixed   $model
     * @param  string  $attribute
     * @return mixed
     */
    public function update(array $data, $model, string $attribute = 'id')
    {
        if ($model instanceof Model) {
            return $model->update($data);
        }

        $this->applySpecs();

        return $this->model->where($attribute, $model)->update($data);
    }

    /**
     * @param  mixed  $model
     * @return mixed
     */
    public function delete($model)
    {
        if ($model instanceof Model) {
            return $model->delete();
        }

        $this->applySpecs();

        return $this->model->destroy($model);
    }

    /**
     * @param  \Guardian360\Repository\Contracts\SpecificationContract  $specification
     * @return $this
     */
    public function pushSpec(Specification $specification)
    {
        $this->specifications[] = $specification;

        return $this;
    }

    /**
     * @return $this
     */
    public function applySpecs()
    {
        foreach ($this->specifications as $specification) {
            $this->model = $specification->apply($this->model);
        }

        return $this;
    }
}