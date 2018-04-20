<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Account type entities.
 */
interface AccountTypeInterface extends ConfigEntityInterface
{

    // Add get/set methods for your configuration properties here.

    /**
     * @return string
     */
    public function getLabel();
}
