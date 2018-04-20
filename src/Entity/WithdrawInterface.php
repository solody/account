<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Withdraw entities.
 *
 * @ingroup finance
 */
interface WithdrawInterface extends ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Withdraw name.
   *
   * @return string
   *   Name of the Withdraw.
   */
  public function getName();

  /**
   * Sets the Withdraw name.
   *
   * @param string $name
   *   The Withdraw name.
   *
   * @return \Drupal\finance\Entity\WithdrawInterface
   *   The called Withdraw entity.
   */
  public function setName($name);

  /**
   * Gets the Withdraw creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Withdraw.
   */
  public function getCreatedTime();

  /**
   * Sets the Withdraw creation timestamp.
   *
   * @param int $timestamp
   *   The Withdraw creation timestamp.
   *
   * @return \Drupal\finance\Entity\WithdrawInterface
   *   The called Withdraw entity.
   */
  public function setCreatedTime($timestamp);

}
