<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Account entities.
 *
 * @ingroup finance
 */
interface AccountInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Account name.
   *
   * @return string
   *   Name of the Account.
   */
  public function getName();

  /**
   * Sets the Account name.
   *
   * @param string $name
   *   The Account name.
   *
   * @return \Drupal\finance\Entity\AccountInterface
   *   The called Account entity.
   */
  public function setName($name);

  /**
   * Gets the Account creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Account.
   */
  public function getCreatedTime();

  /**
   * Sets the Account creation timestamp.
   *
   * @param int $timestamp
   *   The Account creation timestamp.
   *
   * @return \Drupal\finance\Entity\AccountInterface
   *   The called Account entity.
   */
  public function setCreatedTime($timestamp);

}
