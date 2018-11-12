<?php

namespace Guardian360\Repository\Contracts;

interface RepositoryContract
{
    /**
     * @param  array  $columns
     * @return mixed
     */
    public function all(array $columns = ['*']);

    /**
     * @param  mixed  $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $columns
     * @return mixed
     */
    public function findBy(string $attribute, $value, array $columns = ['*']);

    /**
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $columns
     * @return mixed
     */
    public function findAllBy(string $attribute, $value, array $columns = ['*']);

    /**
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(int $perPage = 10, int $page = 1);

    /**
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param  array   $data
     * @param  mixed   $id
     * @param  string  $attribute
     * @return mixed
     */
    public function update(array $data, $id, string $attribute = 'id');

    /**
     * @param  mixed  $id
     * @return mixed
     */
    public function delete($id);
}
