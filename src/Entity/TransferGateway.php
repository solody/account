<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\finance\Plugin\TransferGatewayManager;

/**
 * Defines the Transfer gateway entity.
 *
 * @ConfigEntityType(
 *   id = "finance_transfer_gateway",
 *   label = @Translation("Transfer gateway"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\TransferGatewayListBuilder",
 *     "form" = {
 *       "add" = "Drupal\finance\Form\TransferGatewayForm",
 *       "edit" = "Drupal\finance\Form\TransferGatewayForm",
 *       "delete" = "Drupal\finance\Form\TransferGatewayDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\finance\TransferGatewayHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "finance_transfer_gateway",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_transfer_gateway/{finance_transfer_gateway}",
 *     "add-form" = "/admin/finance/finance_transfer_gateway/add",
 *     "edit-form" = "/admin/finance/finance_transfer_gateway/{finance_transfer_gateway}/edit",
 *     "delete-form" = "/admin/finance/finance_transfer_gateway/{finance_transfer_gateway}/delete",
 *     "collection" = "/admin/finance/finance_transfer_gateway"
 *   }
 * )
 */
class TransferGateway extends ConfigEntityBase implements TransferGatewayInterface {

  /**
   * The Transfer gateway ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Transfer gateway label.
   *
   * @var string
   */
  protected $label;

  /**
   * The plugin ID.
   *
   * @var string
   */
  protected $plugin;

  /**
   * The plugin configuration.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    /** @var TransferGatewayManager $plugin_manager */
    $plugin_manager = \Drupal::service('plugin.manager.transfer_gateway');
    return $plugin_manager->createInstance($this->plugin, $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return $this->plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setPluginId($plugin_id) {
    $this->plugin = $plugin_id;
    $this->configuration = [];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setPluginConfiguration(array $configuration) {
    $this->configuration = $configuration;
    return $this;
  }
}
