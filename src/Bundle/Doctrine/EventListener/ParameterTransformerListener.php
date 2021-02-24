<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Doctrine\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
final class ParameterTransformerListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $parametersMap = [];

    public function __construct(ContainerInterface $container)
    {
        // Circular reference detected for service "doctrine.dbal.default_connection", path: "doctrine.dbal.default_connection".
        $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $settings = $event->getObject();

        if ($settings instanceof SettingsInterface) {
            $this->reverseTransform($settings);
        }
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $entityManager = $event->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof SettingsInterface) {
                $this->transform($entity, $entityManager);
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof SettingsInterface) {
                $this->transform($entity, $entityManager);
            }
        }
    }

    public function postFlush()
    {
        // revert settings parameters to what they were before flushing
        foreach ($this->parametersMap as $map) {
            $map['entity']->setParameters($map['parameters']);
        }

        // reset parameters map
        $this->parametersMap = [];
    }

    private function transform(SettingsInterface $settings, EntityManager $entityManager)
    {
        // store old parameters, so we can revert to it after flush
        $this->parametersMap[] = [
            'entity' => $settings,
            'parameters' => $settings->getParameters(),
        ];

        $transformers = $this->getTransformers($settings);
        foreach ($settings->getParameters() as $name => $value) {
            if (isset($transformers[$name])) {
                $settings->set($name, $transformers[$name]->transform($value));
            }
        }

        $classMetadata = $entityManager->getClassMetadata(get_class($settings));
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadata, $settings);
    }

    private function reverseTransform(SettingsInterface $settings)
    {
        $transformers = $this->getTransformers($settings);
        foreach ($settings->getParameters() as $name => $value) {
            if (isset($transformers[$name])) {
                $settings->set($name, $transformers[$name]->reverseTransform($value));
            }
        }
    }

    /**
     * @return DataTransformerInterface[]
     */
    private function getTransformers(SettingsInterface $settings)
    {
        $registry = $this->container->get('sylius.registry.settings_schema');

        /** @var SchemaInterface $schema */
        $schema = $registry->get($settings->getSchemaAlias());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        return $settingsBuilder->getTransformers();
    }
}
