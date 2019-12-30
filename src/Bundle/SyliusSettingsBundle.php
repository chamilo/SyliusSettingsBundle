<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle;

use Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler\RegisterResolversPass;
use Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler\RegisterSchemasPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusSettingsBundle extends AbstractBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            self::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterSchemasPass());
        $container->addCompilerPass(new RegisterResolversPass());
    }

    protected function getModelNamespace(): ?string
    {
        return 'Sylius\Bundle\SettingsBundle\Model';
    }
}
