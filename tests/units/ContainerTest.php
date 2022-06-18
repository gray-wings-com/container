<?php
declare(strict_types=1);

namespace Graywings\Container\Tests\Units;

use Graywings\Container\Container;
use Graywings\Container\Exceptions\InvalidArgumentException;
use Graywings\Container\Tests\Units\SampleObjects\ImplementedClass;
use Graywings\Container\Tests\Units\SampleObjects\InjectClass;
use Graywings\Container\Tests\Units\SampleObjects\InjectedClass;
use Graywings\Container\Tests\Units\SampleObjects\InjectedInterface;
use Graywings\Container\Tests\Units\SampleObjects\InjectInterface;
use Graywings\Container\Tests\Units\SampleObjects\SampleInterface;
use Graywings\Container\Tests\Units\SampleObjects\NotImplementedClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerTest extends TestCase
{
    function test_constructor() {
        $container = new Container();
    }

    function test_set() {
        $container = new Container();
        $container->set('hello', 'world');
        $this->assertEquals('world', $container->get('hello'));
        $container->set(SampleInterface::class, new ImplementedClass());
        $container->set(InjectInterface::class, new InjectClass());
        $inject = $container->get(InjectInterface::class);
        self::assertTrue(is_a($inject, InjectInterface::class));
        self::assertTrue(is_a($inject, InjectClass::class));
        $container->set(InjectedInterface::class, function(ContainerInterface $c) {
           return new InjectedClass($c->get(InjectInterface::class));
        });
        $injected = $container->get(InjectedInterface::class);
        self::assertTrue(is_a($injected, InjectedInterface::class));
        self::assertTrue(is_a($injected, InjectedClass::class));
        $injectInInjected = $injected->inject();
        self::assertTrue(is_a($injectInInjected, InjectInterface::class));
        self::assertTrue(is_a($injectInInjected, InjectClass::class));
        self::assertTrue($inject === $injectInInjected);
    }

    function test_setDependency() {
        $container = new Container();
        $container->setDependency(
            SampleInterface::class,
            new ImplementedClass()
        );
        $container->get(SampleInterface::class);
    }

    function test_setFactory() {
        $container = new Container();
        $container->setDependency(InjectInterface::class, new InjectClass());
        $container->setFactory(InjectedInterface::class, function(ContainerInterface $c) {
            return new InjectedClass($c->get(InjectInterface::class));
        });
        $injected = $container->get(InjectedInterface::class);
        self::assertTrue(is_a($injected, InjectedInterface::class));
        self::assertTrue(is_a($injected, InjectedClass::class));
        $inject = $injected->inject();
        self::assertTrue(is_a($inject, InjectInterface::class));
        self::assertTrue(is_a($inject, InjectClass::class));
    }

    function test_invalidSetDependency() {
        $this->expectException(InvalidArgumentException::class);
        $container = new Container();
        $container->setDependency(
            SampleInterface::class,
            new NotImplementedClass()
        );
    }
}
