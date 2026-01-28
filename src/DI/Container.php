<?php

namespace PhpFramework\DI;

use Closure;
use PhpFramework\Exceptions\Container\ContainerException;
use PhpFramework\Exceptions\Container\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public function __construct(
        private array $bindings = []
    ) {}

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->bindings[$id];
            if ($entry instanceof Closure) {
                return $entry($this);
            }
            $id = $entry;
        }

        return $this->resolve($id);
    }

    /**
     * @inheritdoc
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    /**
     * Add the $id and $value to bindings
     *
     * @param string $id
     * @param string|Closure $value
     * @return void
     */
    public function set(string $id, string|Closure $value): void
    {
        $this->bindings[$id] = $value;
    }

    /**
     * @param $id
     * @return object
     * @throws ContainerException
     * @throws NotFoundException
     */
    private function resolve($id): object
    {
        $reflection = new \ReflectionClass($id);
        if (! $reflection->isInstantiable()) {
            throw new ContainerException("Class {$id} is not instantiable");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $id;
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new NotFoundException(
                        "Cannot resolve parameter {$parameter->getName()} in class {$id}"
                    );
                }
            } else {
                $dependencies[] = $this->get($type->getName());
            }
        }

        try {
            return $reflection->newInstanceArgs($dependencies);
        } catch (\Exception $e) {
            throw new ContainerException("Class {$id} can not initiate: " . $e->getMessage());
        }
    }
}
