<?php

declare(strict_types=1);

namespace Sylius\Bundle\SettingsBundle\Registry;

/**
 * This exception should be thrown by service registry
 * when given service type does not exist.
 */
class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type, array $existingServices)
    {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices)
        ));
    }
}
