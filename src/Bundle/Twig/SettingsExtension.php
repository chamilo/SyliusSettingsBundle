<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Twig;

use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SettingsExtension extends AbstractExtension
{
    private SettingsHelperInterface $helper;

    public function __construct(SettingsHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions(): array
    {
        return [
             new TwigFunction('sylius_settings', [$this->helper, 'getSettings']),
        ];
    }

    public function getName(): string
    {
        return 'sylius_settings';
    }
}
