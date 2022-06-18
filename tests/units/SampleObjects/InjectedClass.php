<?php
declare(strict_types=1);

namespace Graywings\Container\Tests\Units\SampleObjects;

use Graywings\Container\Inject;
use Graywings\Container\Injectable;

#[Injectable]
class InjectedClass implements InjectedInterface
{
    private $inject;
    #[Inject([InjectInterface::class])]
    public function __construct(InjectInterface $inject)
    {
        $this->inject = $inject;
    }
    public function inject() {
        return $this->inject;
    }
}