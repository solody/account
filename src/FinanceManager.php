<?php

namespace Drupal\finance;

use Drupal\finance\Entity\Account;
use Drupal\finance\Entity\Ledger;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;

/**
 * Class FinanceManager.
 */
class FinanceManager implements FinanceManagerInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Constructs a new FinanceManager object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

    /**
     * @inheritdoc
     */
    public function getAccount(User $user, $type)
    {
        /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
        $query = \Drupal::entityQuery('finance_account')
            ->condition('user_id', $user->id())
            ->condition('type', $type);
        $ids = $query->execute();

        if (!empty($ids)) {
            return Account::load(array_pop($ids));
        } else {
            throw new \Exception('找不到账户');
        }
    }

    /**
     * @inheritdoc
     */
    public function createLedger(
        Account $financeAccount,
        $amountType,
        $amount,
        $remarks = '',
        $source = null)
    {
        // 计算余额
        $last_ledger = $this->getLastLedger($financeAccount);
        $balance = 0;
        if ($last_ledger) {
            $last_ledger->get('balance')[0]->getNumber();
        }

        if ($amountType === Ledger::AMOUNT_TYPE_DEBIT) {
            $balance = $balance + $amount;
        } elseif ($amountType === Ledger::AMOUNT_TYPE_CREDIT) {
            $balance = $balance - $amount;
        }

        $create_data = [
            'finance_account_id' => $financeAccount,
            'amount_type' => $amountType,
            'amount' => [
                'number' => $amount,
                'currency_code' => 'CNY'
            ],
            'balance' => [
                'number' => $balance,
                'currency_code' => 'CNY'
            ],
            'remarks' => $remarks
        ];

        if ($source) $create_data['source'] = $source;

        $ledger = Ledger::create($create_data);
        $ledger->save();

        return $ledger;
    }

    public function getLastLedger(Account $financeAccount)
    {
        /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
        $query = \Drupal::entityQuery('finance_ledger')
            ->condition('account_id', $financeAccount->id())
            ->sort('id', 'DESC');
        $ids = $query->execute();

        if (!empty($ids)) {
            return Account::load(array_pop($ids));
        } else {
            return null;
        }
    }
}
