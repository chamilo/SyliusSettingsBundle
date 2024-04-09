<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Templating\Helper;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;

class SettingsHelper implements SettingsHelperInterface
{
    protected string $charset = 'UTF-8';

    protected SettingsManagerInterface $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function getSettings(string $schemaAlias): SettingsInterface
    {
        return $this->settingsManager->load($schemaAlias);
    }

    public function getName(): string
    {
        return 'sylius_settings';
    }

    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default charset.
     */
    public function getCharset(): string
    {
        return $this->charset;
    }
}
