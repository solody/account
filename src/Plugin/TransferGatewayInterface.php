<?php

namespace Drupal\finance\Plugin;

use Drupal\entity\BundlePlugin\BundlePluginInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Plugin\PluginWithFormsInterface;

/**
 * Defines an interface for Transfer gateway plugins.
 */
interface TransferGatewayInterface extends PluginWithFormsInterface, ConfigurablePluginInterface, PluginFormInterface, BundlePluginInterface
{


    // Add get/set methods for your plugin type here.
    /**
     * 转账
     *
     * @param $transfer_to array  转账目标
     * @param $amount float   金额
     * @return mixed
     */
    public function transfer($transfer_to, $amount);
}
