<?php

namespace Drupal\account;

use Drupal\account\Entity\TransferMethodInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines the order storage.
 */
class TransferMethodStorage extends SqlContentEntityStorage {

  public function loadDefault($user_id) {
    $methods = $this->loadByProperties([
      'user_id' => $user_id,
      'is_default' => true
    ]);

    if (count($methods)) return reset($methods);
    else return false;
  }

  public function setDefault(TransferMethodInterface $default_method, $user_id) {
    if ($default_method->getOwnerId() !== $user_id)
      throw new \Exception('This transfer method doesn\'t belong to user '.$user_id.'.');

    /** @var TransferMethodInterface[] $methods */
    $methods = $this->loadByProperties([
      'user_id' => $user_id
    ]);

    $saved_default = null;
    foreach ($methods as $method) {
      if ($method->isDefault() && $method->id() !== $default_method->id()) $method->setDefault(false)->save();
      if (!$method->isDefault() && $method->id() === $default_method->id()) {
        $method->setDefault(true)->save();
        $saved_default = $method;
      } else {
        $saved_default = $default_method;
      }
    }

    if ($saved_default === null) $default_method->setDefault(true)->save();

    return $saved_default;
  }
}
