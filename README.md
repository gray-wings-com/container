# Graywings/Container

## DI-Container
Graywings/Container is a lightweight DI container.

## Example: Autowiring
```php
<?php
declare(strict_types=1);

namespace Graywings\Container\Tests\Units\SampleObjects;

use Graywings\Container\Injectable;
use Graywings\Container\Inject;

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build(
    [
        'db.host' => 'db',
        InjectInterface::class => InjectClass::class,
        InjectedInterface::class => InjectedClass::class
    ]
);

$container->get('db.host'); // 'db'
$container->get(InjectInterface::class) // Instanceof InjectClass
$container->get(InjectedInterface::class) // Instanceof InjectedClass

interface InjectInterface
{
}

interface InjectedInterface
{
}

#[Injectable]
class InjectClass implements InjectInterface
{
}

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
```
