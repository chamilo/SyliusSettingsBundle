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
use Symfony\Component\Templating\Helper\Helper;

final class SettingsHelper extends Helper implements SettingsHelperInterface
{
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function getSettings($schemaAlias): SettingsInterface
    {
        return $this->settingsManager->load($schemaAlias);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_settings';
    }
}
