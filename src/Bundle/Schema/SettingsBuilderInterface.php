<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsBuilderInterface
{
    /**
     * @return DataTransformerInterface[]
     */
    public function getTransformers();

    /**
     * @param string $parameterName
     */
    public function setTransformer($parameterName, DataTransformerInterface $transformer);
}
