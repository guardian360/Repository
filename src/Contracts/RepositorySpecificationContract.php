<?php

namespace Guardian360\Repository\Contracts;

use Guardian360\Repository\Contracts\SpecificationContract as Specification;

interface RepositorySpecificationContract
{
    /**
     * Push a specification to filter the query.
     *
     * @param  \Guardian360\Repository\Contracts\SpecificationContract
     * @return $this
     */
    public function pushSpec(Specification $specification);
}
