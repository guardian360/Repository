<?php

namespace Guardian360\Repository\Tests\Specifications;

use Guardian360\Repository\Contracts\SpecificationContract as Specification;

class UserIsAdmin implements Specification
{
    /**
     * Apply the specification.
     *
     * @param  \Guardian360\Repository\Tests\Models\User  $model
     * @return \Guardian360\Repository\Tests\Models\User
     */
    public function apply($user)
    {
        return $user->where('role', 'admin');
    }
}
