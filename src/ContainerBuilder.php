<?php
declare(strict_types=1);

namespace Graywings\Container;

use Graywings\Container\Exceptions\InvalidArgumentException;
use Graywings\Container\Exceptions\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;

class ContainerBuilder
{
    /**
     * @var bool $useAutowire
     */
    private readonly bool $useAutowire;

    /**
     * @param bool $useAutowire
     */
    public function __construct(
        bool  $useAutowire = true
    )
    {
        $this->useAutowire = $useAutowire;
    }

    /**
     * @param array $containerSettings
     * @return Container
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     */
    public function build(
        array $containerSettings
    ): Container
    {
        $container = new Container();
        foreach ($containerSettings as $key => $value) {
            if ($this->useAutowire && interface_exists($key)) {
                $this->autowire($container, $key, $value);
            } else {
                $container->set($key, $value);
            }
        }
        return $container;
    }

    /**
     * @param Container $container
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function autowire(
        Container $container,
        string $key,
        mixed $value
    ): void
    {
        try {
            $reflection = new ReflectionClass($value);
        } catch(ReflectionException $e) {
            throw new InvalidArgumentException(
                'Can\'t set normal value to existing interface name.',
                0,
                $e
            );
        }
        $attributes = $reflection->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Injectable::class) {
                $constructor = $reflection->getConstructor();
                $injectionInstances = [];
                if ($constructor !== null) {
                    $constructorAttributes = $constructor->getAttributes();
                    foreach ($constructorAttributes as $constructorAttribute) {
                        if ($constructorAttribute->getName() === Inject::class) {
                            $injectInfo = new Inject(...$constructorAttribute->getArguments());
                            $injectTargets = $injectInfo->targets();
                            foreach ($injectTargets as $target) {
                                $injectionInstances[] = $container->get($target);
                            }
                        }
                    }
                }
                try {
                    $container->setDependency($key, $reflection->newInstance(...$injectionInstances));
                } catch (ReflectionException $e) {
                    throw new InvalidArgumentException(
                        'Can\'t set normal value to existing interface name.',
                        0,
                        $e
                    );
                }
            }
        }
    }
}