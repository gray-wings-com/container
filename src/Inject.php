<?php
declare(strict_types=1);

namespace Graywings\Container;

use Attribute;

#[Attribute]
class Inject
{
    public function __construct(array $injectIds)
    {
        $this->ids = $injectIds;
    }
    public function targets(): array {
        return $this->ids;
    }
}