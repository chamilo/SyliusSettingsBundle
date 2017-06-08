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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SettingsBuilderInterface extends OptionsResolverInterface
{
    /**
     * @return DataTransformerInterface[]
     */
    public function getTransformers();

    /**
     * @param string $parameterName
     * @param DataTransformerInterface $transformer
     */
    public function setTransformer($parameterName, DataTransformerInterface $transformer);
}
