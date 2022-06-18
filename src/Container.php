<?php
declare(strict_types=1);

namespace Graywings\Container;

use Graywings\Container\Exceptions\InvalidArgumentException;
use Graywings\Container\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array<string, mixed> $entries
     */
    protected array $entries = [];
    /**
     * @var array<string, mixed> $resolvedEntries
     */
    protected array $resolvedEntries = [];

    /**
     * @param string $id
     * @param mixed $value
     * @return void
     */
    public function set(
        string $id,
        mixed  $value
    ): void
    {
        if (interface_exists($id)) {
            if (is_callable($value)) {
                $this->setFactory($id, $value);
            } else {
                $this->setDependency($id, $value);
            }
        } else if(is_scalar($value)) {
            $this->setValue($id, $value);
        } else {
            throw new InvalidArgumentException();
        }
    }

    public function setValue(
        string $id,
        mixed $scalar
    ): void {
        if (interface_exists($id)) {
            throw new InvalidArgumentException();
        } else {
            $this->resolvedEntries[$id] = $scalar;
        }
    }

    public function setDependency(
        string $interfaceClassName,
        mixed  $object
    ): void
    {
        $this->checkObjectImplementedInterface($interfaceClassName, $object);
        $this->resolvedEntries[$interfaceClassName] = $object;
    }

    public function setFactory(
        string   $interfaceClassname,
        callable $factory
    ): void
    {
        $this->entries[$interfaceClassname] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Container don\'t has ' . $id);
        } else if ($this->isResolved($id)) {
            return $this->resolvedEntries[$id];
        } else {
            $resolved = $this->entries[$id]($this);
            $this->checkObjectImplementedInterface($id, $resolved);
            $this->resolvedEntries[$id] = $resolved;
            unset($this->entries[$id]);
            return $this->resolvedEntries[$id];
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return $this->isResolved($id) || $this->isRegistered($id);
    }

    /**
     * @param string $interfaceClassName
     * @param mixed $object
     * @return void
     * @throws InvalidArgumentException
     */
    private function checkObjectImplementedInterface(
        string $interfaceClassName,
        mixed  $object
    ): void
    {
        if (!array_search(
            $interfaceClassName,
            class_implements($object)
        )) {
            throw new InvalidArgumentException(
                'Object ' . get_class($object) . ' not implemented ' . $interfaceClassName
            );
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    private function isResolved(string $id): bool
    {
        return array_key_exists($id, $this->resolvedEntries);
    }

    /**
     * @param string $id
     * @return bool
     */
    private function isRegistered(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}