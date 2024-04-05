<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

use Sylius\Bundle\SettingsBundle\Exception\ParameterNotFoundException;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingsInterface extends \ArrayAccess, \Countable //ResourceInterface,
{
    public function getSchemaAlias(): string;

    public function setSchemaAlias(string $schemaAlias);

    public function getNamespace(): string;

    public function setNamespace(string $namespace): void;

    public function getParameters(): array;

    public function setParameters(array $parameters): void;

    /**
     * @throws ParameterNotFoundException
     */
    public function get(string $name): mixed;

    public function has(string $name): bool;

    public function set(string $name, mixed $value): void;

    /**
     * @throws ParameterNotFoundException
     */
    public function remove(string $name): void;
}
