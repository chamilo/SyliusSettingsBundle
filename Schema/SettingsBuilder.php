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
class SettingsBuilder extends AbstractSettingsBuilder
{
    /**
     * @var DataTransformerInterface[]
     */
    protected $transformers = [];

    /**
     * {@inheritdoc}
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformer($parameterName, DataTransformerInterface $transformer)
    {
        $this->transformers[$parameterName] = $transformer;

        return $this;
    }
}
