<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Manager;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\SettingsBundle\Event\SettingsEvent;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Registry\ServiceRegistryInterface;
use Sylius\Bundle\SettingsBundle\Resolver\SettingsResolverInterface;
use Sylius\Bundle\SettingsBundle\Resource\FactoryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
final class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $schemaRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    private $resolverRegistry;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var FactoryInterface
     */
    private $settingsFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        ServiceRegistryInterface $schemaRegistry,
        ServiceRegistryInterface $resolverRegistry,
        ObjectManager $manager,
        FactoryInterface $settingsFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->schemaRegistry = $schemaRegistry;
        $this->resolverRegistry = $resolverRegistry;
        $this->manager = $manager;
        $this->settingsFactory = $settingsFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $schemaAlias, string $namespace = null, bool $ignoreUnknown = true): SettingsInterface
    {
        /** @var SchemaInterface $schema */
        $schema = $this->schemaRegistry->get($schemaAlias);

        /** @var SettingsResolverInterface $resolver */
        $resolver = $this->resolverRegistry->get($schemaAlias);

        $settings = $resolver->resolve($schemaAlias, $namespace);

        if (!$settings) {
            $settings = $this->settingsFactory->createNew();
            $settings->setSchemaAlias($schemaAlias);
        }

        // We need to get a plain parameters array since we use the options resolver on it
        $parameters = $settings->getParameters();

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        // Remove unknown settings' parameters (e.g. From a previous version of the settings schema)
        if ($ignoreUnknown) {
            foreach ($parameters as $name => $value) {
                if (!$settingsBuilder->isDefined($name)) {
                    unset($parameters[$name]);
                }
            }
        }

        $parameters = $settingsBuilder->resolve($parameters);
        $settings->setParameters($parameters);

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SettingsInterface $settings)
    {
        /** @var SchemaInterface $schema */
        $schema = $this->schemaRegistry->get($settings->getSchemaAlias());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $parameters = $settingsBuilder->resolve($settings->getParameters());
        $settings->setParameters($parameters);

        $this->eventDispatcher->dispatch(SettingsEvent::PRE_SAVE, new SettingsEvent($settings));

        $this->manager->persist($settings);
        $this->manager->flush();

        $this->eventDispatcher->dispatch(SettingsEvent::POST_SAVE, new SettingsEvent($settings));
    }
}
