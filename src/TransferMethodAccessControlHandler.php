<?php

namespace Drupal\account;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Transfer method entity.
 *
 * @see \Drupal\account\Entity\TransferMethod.
 */
class TransferMethodAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\account\Entity\TransferMethodInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view transfer method entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit transfer method entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete transfer method entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add transfer method entities');
  }

}
