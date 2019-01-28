<?php

namespace Drupal\account\Entity;

use Drupal\commerce_price\Price;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Account entities.
 *
 * @ingroup account
 */
interface AccountInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface
{

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
     * @return \Drupal\account\Entity\AccountInterface
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
     * @return \Drupal\account\Entity\AccountInterface
     *   The called Account entity.
     */
    public function setCreatedTime($timestamp);

    /**
     * @return Price
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function getBalance();

    /**
     * @param Price $amount
     * @return $this
     */
    public function setBalance(Price $amount);

    /**
     * @return Price
     */
    public function getTotalCredit();

    /**
     * @param Price $amount
     * @return $this
     */
    public function setTotalCredit(Price $amount);

    /**
     * @return Price
     */
    public function getTotalDebit();

    /**
     * @param Price $amount
     * @return $this
     */
    public function setTotalDebit(Price $amount);
}
