<?php
declare(strict_types=1);

namespace Graywings\Container\Tests\Units;

use Graywings\Container\ContainerBuilder;
use Graywings\Container\Tests\Units\SampleObjects\InjectClass;
use Graywings\Container\Tests\Units\SampleObjects\InjectedClass;
use Graywings\Container\Tests\Units\SampleObjects\InjectedInterface;
use Graywings\Container\Tests\Units\SampleObjects\InjectInterface;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    function test_build() {
        $containerBuilder = new ContainerBuilder(
            [
                InjectInterface::class => InjectClass::class,
                InjectedInterface::class => InjectedClass::class
            ]
        );
        $container = $containerBuilder->build();
        $inject = $container->get(InjectInterface::class);
        var_dump($inject);
        $injected = $container->get(InjectedInterface::class);
        var_dump($injected);
    }
}
