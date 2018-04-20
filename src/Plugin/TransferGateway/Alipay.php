<?php
namespace Drupal\finance\Plugin\TransferGateway;

use Drupal\finance\Plugin\TransferGatewayBase;
use Drupal\entity\BundleFieldDefinition;

/**
 * @TransferGateway(
 *   id = "alipay",
 *   label = @Translation("Alipay")
 * )
 */
class Alipay extends TransferGatewayBase
{
    /**
     * @inheritdoc
     */
    public function buildFieldDefinitions()
    {
        $fields['alipay_account'] = BundleFieldDefinition::create('string')
            ->setLabel(t('转账目标账号'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => -9,
            ]);

        $fields['alipay_name'] = BundleFieldDefinition::create('string')
            ->setLabel(t('转账目标姓名'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => -9,
            ]);

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function transfer($transfer_to, $amount)
    {
        // TODO: Implement transfer() method.
    }
}