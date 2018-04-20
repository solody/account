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
     * 创建账户，如果账户已存在，直接返回该账户
     *
     * @param User $user
     * @param $type
     * @return Account
     */
    public function createAccount(User $user, $type);

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
     * @param $source
     * @return Ledger
     */
    public function createLedger(Account $financeAccount,
                                 $amountType,
                                 $amount,
                                 $remarks = '',
                                 $source = null);
}
