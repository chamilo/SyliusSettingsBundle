<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

use Sylius\Bundle\SettingsBundle\Exception\ParameterNotFoundException;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Settings implements SettingsInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $schemaAlias;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $parameters = [];

    public function getId()
    {
        return $this->id;
    }

    public function getSchemaAlias(): string
    {
        return $this->schemaAlias;
    }

    public function setSchemaAlias(string $schemaAlias): void
    {
        if (null !== $this->schemaAlias) {
            throw new \LogicException('The schema alias of the settings model is immutable, instantiate a new object in order to use another schema.');
        }

        $this->schemaAlias = $schemaAlias;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace($namespace): void
    {
        if (null !== $this->namespace) {
            throw new \LogicException('The namespace of the settings model is immutable, instantiate a new object in order to use another namespace.');
        }

        $this->namespace = $namespace;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    public function get(string $name): mixed
    {
        if (!$this->has($name)) {
            throw new ParameterNotFoundException($name);
        }

        return $this->parameters[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function set(string $name, mixed $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function remove(string $name): void
    {
        if (!$this->has($name)) {
            throw new ParameterNotFoundException($name);
        }

        unset($this->parameters[$name]);
    }
}
