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

    /**
     * Applies specifications to the query.
     *
     * @return $this
     */
    public function applySpecs();
}
