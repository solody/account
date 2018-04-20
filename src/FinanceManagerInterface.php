<?php

namespace Drupal\finance;

use Drupal\finance\Entity\Account;
use Drupal\finance\Entity\Ledger;
use Drupal\user\Entity\User;

/**
 * Interface FinanceManagerInterface.
 */
interface FinanceManagerInterface {

    /**
     * 获取一个账户
     *
     * @param User $user
     * @param $type
     * @return Account
     */
    public function getAccount(User $user, $type);

    /**
     * 增加记账记录
     *
     * @param Account $financeAccount
     * @param $amountType
     * @param $amount
     * @param $remarks
     * @param $order_id
     * @param $withdraw_id
     * @return Ledger
     */
    public function createLedger(Account $financeAccount,
                                 $amountType,
                                 $amount,
                                 $remarks = '',
                                 $source = null);
}
