<?php

namespace Drupal\finance\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\distribution\DistributionManager;

/**
 * Publishes a product.
 *
 * @Action(
 *   id = "finance_transfer_withdraw",
 *   label = @Translation("Transfer withdraw"),
 *   type = "finance_withdraw"
 * )
 */
class TransferWithdraw extends ActionBase
{

    /**
     * {@inheritdoc}
     */
    public function execute($entity = NULL)
    {
        /** @var \Drupal\finance\Entity\Withdraw $entity */

        /** @var DistributionManager $distribution_manager */
        $distribution_manager = \Drupal::getContainer()->get('distribution.distribution_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE)
    {
        /** @var \Drupal\finance\Entity\Withdraw $object */
        $result = $object
            ->access('update', $account, TRUE)
            ->andIf($object->status->access('edit', $account, TRUE));

        return $return_as_object ? $result : $result->isAllowed();
    }

}
