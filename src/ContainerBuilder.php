<?php
declare(strict_types=1);

namespace Graywings\Container;

use ReflectionClass;

class ContainerBuilder
{
    /**
     * @var array<string, mixed> $containerSettings
     */
    private readonly array $containerSettings;
    /**
     * @var bool $useAutoWire
     */
    private readonly bool $useAutoWire;

    public function __construct(
        array $containerSettings,
        bool  $useAutowire = true
    )
    {
        $this->containerSettings = $containerSettings;
        $this->useAutoWire = $useAutowire;
    }

    public function build(): Container
    {
        $container = new Container();
        foreach ($this->containerSettings as $key => $value) {
            if ($this->useAutoWire) {
                $reflection = new ReflectionClass($value);
                $attributes = $reflection->getAttributes();
                foreach ($attributes as $attribute) {
                    if ($attribute->getName() === Injectable::class) {
                        $constructor = $reflection->getConstructor();
                        if ($constructor !== null) {
                            $constructorAttributes = $constructor->getAttributes();
                            foreach ($constructorAttributes as $constructorAttribute) {
                                if ($constructorAttribute->getName() === Inject::class) {
                                    $injectInfo = new Inject(...$constructorAttribute->getArguments());
                                    $injectTargets = $injectInfo->targets();
                                    $injectionInstances = [];
                                    foreach ($injectTargets as $target) {
                                        $injectionInstances[] = $container->get($target);
                                    }
                                    $container->setDependency($key, $reflection->newInstance(...$injectionInstances));
                                } else {
                                    $container->setDependency($key, $reflection->newInstance());
                                }
                            }
                        } else {
                            $container->setDependency($key, $reflection->newInstance());
                        }
                    }
                }

            }
        }
        return $container;
    }
}