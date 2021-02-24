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

use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Julio Montoya
 */
interface SettingsManagerInterface
{
    public function load(string $schemaAlias, string $namespace = null, bool $ignoreUnknown = true): SettingsInterface;

    public function save(SettingsInterface $settings);
}
