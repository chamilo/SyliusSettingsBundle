<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Sylius\Bundle\SettingsBundle\Registry\ServiceRegistryInterface;

/**
 * Cannot be final, because it is proxied.
 *
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ResolverServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $decoratedRegistry;

    /**
     * @var SettingsResolverInterface
     */
    private $defaultResolver;

    public function __construct(ServiceRegistryInterface $decoratedRegistry, SettingsResolverInterface $defaultResolver)
    {
        $this->decoratedRegistry = $decoratedRegistry;
        $this->defaultResolver = $defaultResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->decoratedRegistry->all();
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $type, $service): void
    {
        $this->decoratedRegistry->register($type, $service);
    }

    /**
     * {@inheritdoc}
     */
    public function unregister(string $type): void
    {
        $this->decoratedRegistry->unregister($type);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type): bool
    {
        return $this->decoratedRegistry->has($type);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $type)
    {
        if (!$this->decoratedRegistry->has($type)) {
            return $this->defaultResolver;
        }

        return $this->decoratedRegistry->get($type);
    }
}
