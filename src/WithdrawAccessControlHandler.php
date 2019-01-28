<?php

namespace Drupal\account;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Withdraw entity.
 *
 * @see \Drupal\account\Entity\Withdraw.
 */
class WithdrawAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\account\Entity\WithdrawInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view withdraw entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit withdraw entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete withdraw entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add withdraw entities');
  }

}
