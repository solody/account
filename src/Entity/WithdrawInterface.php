<?php

namespace Drupal\finance\Entity;

use Drupal\commerce_price\Price;
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
   * Gets the Withdraw transaction_number.
   *
   * @return string
   *   TransactionNumber of the Withdraw.
   */
  public function getTransactionNumber();

  /**
   * Sets the Withdraw transaction_number.
   *
   * @param string $transaction_number
   *   The Withdraw transaction_number.
   *
   * @return \Drupal\finance\Entity\WithdrawInterface
   *   The called Withdraw entity.
   */
  public function setTransactionNumber($transaction_number);

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


  /**
   * @return Price
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getAmount();

  /**
   * @return Account
   */
  public function getAccount();

  /**
   * @param TransferMethodInterface $transfer_method
   * @return $this
   */
  public function setTransferMethod(TransferMethodInterface $transfer_method);

  /**
   * @return TransferMethodInterface
   */
  public function getTransferMethod();
}
