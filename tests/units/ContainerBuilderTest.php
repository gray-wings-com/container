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
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->build(
            [
                'db.host' => 'db',
                InjectInterface::class => InjectClass::class,
                InjectedInterface::class => InjectedClass::class
            ]
        );
        $dbHost = $container->get('db.host');
        self::assertEquals('db', $dbHost);
        $inject = $container->get(InjectInterface::class);
        self::assertTrue(is_a($inject, InjectInterface::class));
        $injected = $container->get(InjectedInterface::class);
        self::assertTrue(is_a($injected, InjectedInterface::class));
    }

    function test_buildNotUseAutowire() {
        $containerBuilder = new ContainerBuilder(false);
        $container = $containerBuilder->build(
            [
                'db.host' => 'db',
                InjectInterface::class => new InjectClass(),
                InjectedInterface::class => function ($c) {
                    return new InjectedClass($c->get(InjectInterface::class));
                }
            ]
        );
        $dbHost = $container->get('db.host');
        self::assertEquals('db', $dbHost);
        $inject = $container->get(InjectInterface::class);
        self::assertTrue(is_a($inject, InjectInterface::class));
        $injected = $container->get(InjectedInterface::class);
        self::assertTrue(is_a($injected, InjectedInterface::class));
    }
}
