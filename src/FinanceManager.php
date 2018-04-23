<?php

namespace Drupal\finance;

use Drupal\commerce_price\Price;
use Drupal\Core\Session\AccountInterface;
use Drupal\finance\Entity\Account;
use Drupal\finance\Entity\AccountType;
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
    public function getAccount(AccountInterface $user, $type)
    {
        /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
        $query = \Drupal::entityQuery('finance_account')
            ->condition('user_id', $user->id())
            ->condition('type', $type);
        $ids = $query->execute();

        if (!empty($ids)) {
            return Account::load(array_pop($ids));
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
    public function createLedger(
        Account $financeAccount,
        $amountType,
        Price $amount,
        $remarks = '',
        $source = null)
    {
        // 计算余额
        $balance = new Price('0.00', 'CNY');
        $last_ledger = $this->getLastLedger($financeAccount);
        if ($last_ledger) {
            $balance = $last_ledger->getBalance();
        }

        if ($amountType === Ledger::AMOUNT_TYPE_DEBIT) {
            $balance = $balance->add($amount);
        } elseif ($amountType === Ledger::AMOUNT_TYPE_CREDIT) {
            $balance = $balance->subtract($amount);
        }

        $create_data = [
            'account_id' => $financeAccount,
            'amount_type' => $amountType,
            'amount' => $amount,
            'balance' => $balance,
            'remarks' => $remarks
        ];

        if ($source) $create_data['source'] = $source;

        $ledger = Ledger::create($create_data);
        $ledger->save();

        return $ledger;
    }

    /**
     * @param Account $financeAccount
     * @return Ledger|null
     */
    public function getLastLedger(Account $financeAccount)
    {
        /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
        $query = \Drupal::entityQuery('finance_ledger')
            ->condition('account_id', $financeAccount->id())
            ->sort('id', 'DESC')
            ->range(0,1);
        $ids = $query->execute();

        if (!empty($ids)) {
            return Ledger::load(array_pop($ids));
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function createAccount(AccountInterface $user, $type)
    {
        $account = $this->getAccount($user, $type);

        if (!$account) {
            $account_type = AccountType::load($type);
            $price = new Price('0.00', 'CNY');

            $account = Account::create([
                'user_id' => $user->id(),
                'type' => $type,
                'name' => $account_type->getLabel(),
                'total_debit' => $price,
                'total_credit' => $price,
                'balance' => $price
            ]);

            $account->save();
        }

        return $account;
    }
}
