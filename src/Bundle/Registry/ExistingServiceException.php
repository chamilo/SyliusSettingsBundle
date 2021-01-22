<?php

declare(strict_types=1);

namespace Sylius\Bundle\SettingsBundle\Registry;

/**
 * This exception should be thrown by service registry
 * when given type already exists.
 */
class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type)
    {
        parent::__construct(sprintf('%s of type "%s" already exists.', $context, $type));
    }
}
