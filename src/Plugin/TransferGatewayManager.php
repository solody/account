<?php

namespace Drupal\account\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Transfer gateway plugin manager.
 */
class TransferGatewayManager extends DefaultPluginManager {


  /**
   * Constructs a new TransferGatewayManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/TransferGateway', $namespaces, $module_handler, 'Drupal\account\Plugin\TransferGatewayInterface', 'Drupal\account\Annotation\TransferGateway');

    $this->alterInfo('account_transfer_gateway_info');
    $this->setCacheBackend($cache_backend, 'account_transfer_gateway_plugins');
  }

}
