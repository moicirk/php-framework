<?php

namespace PhpFramework\DI;

use PhpFramework\Exceptions\Container\ContainerException;
use PhpFramework\Exceptions\Container\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public function __construct(
        private array $bindings = []
    )
    {
        $this->bindDirectory(APP_ROOT . '/application/Controllers', 'App\Controllers');
    }

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->bindings[$id];
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
     * @param string $value
     * @return void
     */
    public function set(string $id, string $value): void
    {
        $this->bindings[$id] = $value;
    }

    /**
     * Add folder with all classes to binding
     *
     * @param string $directory
     * @param string $namespace
     * @return void
     * @throws ContainerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function bindDirectory(string $directory, string $namespace): void
    {
        $files = glob($directory . '/*.php');

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $className = null;

            if (preg_match('/class\s+(\w+)/i', $content, $matches)) {
                $className = $namespace . '\\' . $matches[1];
            }

            if (!$className || !class_exists($className)) {
                continue;
            }

            $this->bindings[$className] = $className;
            $this->resolve($className);
        }
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
