<?php

declare(strict_types=1);

namespace Sylius\Bundle\SettingsBundle\Registry;

/**
 * Cannot be final, because it is proxied.
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /** @var array */
    private $services = [];

    /**
     * Interface or parent class which is required by all services.
     *
     * @var string
     */
    private $className;

    /**
     * Human readable context for these services, e.g. "grid field".
     *
     * @var string
     */
    private $context;

    public function __construct(string $className, string $context = 'service')
    {
        $this->className = $className;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $identifier, $service): void
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('%s needs to be an object, %s given.', ucfirst($this->context), gettype($service)));
        }

        if (!$service instanceof $this->className) {
            throw new \InvalidArgumentException(sprintf('%s needs to be of type "%s", "%s" given.', ucfirst($this->context), $this->className, get_class($service)));
        }

        $this->services[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister(string $identifier): void
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        unset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $identifier): bool
    {
        return isset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        return $this->services[$identifier];
    }
}
