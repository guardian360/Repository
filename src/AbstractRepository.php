<?php

namespace Guardian360\Repository;

use Illuminate\Pagination\Paginator;
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
     * @var \Illuminate\Database\Eloquent\Model|mixed
     */
    protected $model;

    /**
     * @var array
     */
    protected $with = [];

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
     * @return \Illuminate\Database\Eloquent\Model|mixed
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildQuery()
    {
        $query = $this->model->newQuery();

        if (count($this->with) > 0) {
            $query = $query->with($this->with);
        }

        foreach ($this->specifications as $specification) {
            $query = $specification->apply($query);
        }

        return $query;
    }

    /**
     * @return []\Illuminate\Database\Eloquent\Model|mixed
     */
    public function all(array $columns = ['*'])
    {
        return $this->buildQuery()->get($columns);
    }

    /**
     * @return \Illuminate\Support\LazyCollection
     */
    public function cursor(array $columns = ['*'])
    {
        return $this->buildQuery()->select($columns)->cursor();
    }

    /**
     * @param  mixed  $id
     * @return \Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function find($id)
    {
        return $this->buildQuery()->find($id);
    }

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @return \Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function findBy(string $attribute, $value, array $columns = ['*'])
    {
        return $this->buildQuery()->where($attribute, $value)->first($columns);
    }

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @return []\Illuminate\Database\Eloquent\Model|mixed
     */
    public function findAllBy(string $attribute, $value, array $columns = ['*'])
    {
        if (is_array($value)) {
            return $this->buildQuery()->whereIn($attribute, $value)->get($columns);
        }

        return $this->buildQuery()->where($attribute, $value)->get($columns);
    }

    /**
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate(int $perPage = 10, int $page = 1)
    {
        Paginator::currentPageResolver(function () use ($perPage, $page) {
            $start = ($page - 1) * $perPage;

            return $start / $perPage + 1;
        });

        return $this->buildQuery()->paginate($perPage);
    }

    /**
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function create(array $data)
    {
        return $this->buildQuery()->create($data);
    }

    /**
     * @param  array   $data
     * @param  mixed   $model
     * @param  string  $attribute
     * @return bool
     */
    public function update(array $data, $model, string $attribute = 'id')
    {
        if ($model instanceof Model) {
            return $model->update($data);
        }

        $model = $this->buildQuery()->where($attribute, $model)->first();

        if (!$model) {
            return false;
        }

        return $model->update($data);
    }

    /**
     * @param  array  $data
     * @return mixed
     */
    public function updateOrCreate(array $data)
    {
        return $this->buildQuery()->updateOrCreate($data);
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

        return $this->model->destroy($model);
    }

    /**
     * @param  array  $with
     * @return $this
     */
    public function with(array $with)
    {
        $this->with = $with;

        return $this;
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
    public function flushSpecs()
    {
        $this->specifications = [];

        return $this;
    }
}
