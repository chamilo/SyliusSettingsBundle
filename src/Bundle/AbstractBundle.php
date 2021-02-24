<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\SettingsBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractBundle extends Bundle
{
    public const DRIVER_DOCTRINE_ORM = 'doctrine/orm';

    /**
     * Configure format of mapping files.
     *
     * @var string
     */
    protected $mappingFormat = 'xml';

    public function getSupportedDrivers(): array
    {
        return [
            self::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        if (null !== $this->getModelNamespace()) {
            foreach ($this->getSupportedDrivers() as $driver) {
                [$compilerPassClassName, $compilerPassMethod] = $this->getMappingCompilerPassInfo($driver);

                if (class_exists($compilerPassClassName)) {
                    if (!method_exists($compilerPassClassName, $compilerPassMethod)) {
                        throw new InvalidConfigurationException("The 'mappingFormat' value is invalid, must be 'xml', 'yml' or 'annotation'.");
                    }

                    switch ($this->mappingFormat) {
                        case 'xml':
                        case 'yaml':
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getConfigFilesPath() => $this->getModelNamespace()],
                                [$this->getObjectManagerParameter()],
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));

                            break;
                        case 'annotation':
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getModelNamespace()],
                                [$this->getConfigFilesPath()],
                                [sprintf('%s.object_manager', $this->getBundlePrefix())],
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));

                            break;
                    }
                }
            }
        }
    }

    /**
     * Return the prefix of the bundle.
     */
    protected function getBundlePrefix(): string
    {
        return Container::underscore(substr((string) strrchr(get_class($this), '\\'), 1, -6));
    }

    /**
     * Return the directory where are stored the doctrine mapping.
     */
    protected function getDoctrineMappingDirectory(): string
    {
        return 'model';
    }

    /**
     * Return the entity namespace.
     *
     * @return string
     */
    protected function getModelNamespace(): ?string
    {
        return (new \ReflectionClass($this))->getNamespaceName().'\\Model';
    }

    /**
     * Return mapping compiler pass class depending on driver.
     */
    protected function getMappingCompilerPassInfo(string $driverType): array
    {
        switch ($driverType) {
            case SyliusSettingsBundle::DRIVER_DOCTRINE_ORM:
                $mappingsPassClassname = DoctrineOrmMappingsPass::class;

                break;
            default:
                throw new Exception("Driver not supported : $driverType");
        }

        $compilerPassMethod = sprintf('create%sMappingDriver', ucfirst($this->mappingFormat));

        return [$mappingsPassClassname, $compilerPassMethod];
    }

    /**
     * Return the absolute path where are stored the doctrine mapping.
     */
    protected function getConfigFilesPath(): string
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory())
        );
    }

    protected function getObjectManagerParameter(): string
    {
        return sprintf('%s.object_manager', $this->getBundlePrefix());
    }
}
