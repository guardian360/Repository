<?php

namespace Guardian360\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SpecificationContract
{
    /**
     * Apply the specification.
     *
     * @param  mixed  $query
     * @return mixed
     */
    public function apply($query);
}
