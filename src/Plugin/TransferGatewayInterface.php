<?php

namespace Drupal\finance\Plugin;

use Drupal\entity\BundlePlugin\BundlePluginInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Plugin\PluginWithFormsInterface;
use Drupal\finance\Entity\WithdrawInterface;

/**
 * Defines an interface for Transfer gateway plugins.
 */
interface TransferGatewayInterface extends PluginWithFormsInterface, ConfigurablePluginInterface, PluginFormInterface, BundlePluginInterface {

  /**
   * 转账
   * @param WithdrawInterface $withdraw
   * @return mixed
   */
  public function transfer(WithdrawInterface $withdraw);
}
