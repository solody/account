<?php

namespace Drupal\finance\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Transfer gateway item annotation object.
 *
 * @see \Drupal\finance\Plugin\TransferGatewayManager
 * @see plugin_api
 *
 * @Annotation
 */
class TransferGateway extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
