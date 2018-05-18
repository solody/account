<?php

namespace Drupal\finance\EventSubscriber;

use Drupal\finance\Entity\Ledger;
use Drupal\finance\Entity\Withdraw;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\finance\FinanceManagerInterface;

/**
 * Class WithdrawSubscriber.
 */
class WithdrawSubscriber implements EventSubscriberInterface
{

    /**
     * Drupal\finance\FinanceManagerInterface definition.
     *
     * @var \Drupal\finance\FinanceManagerInterface
     */
    protected $financeFinanceManager;

    /**
     * Constructs a new WithdrawSubscriber object.
     */
    public function __construct(FinanceManagerInterface $finance_finance_manager)
    {
        $this->financeFinanceManager = $finance_finance_manager;
    }

    /**
     * {@inheritdoc}
     */
    static function getSubscribedEvents()
    {
        $events['finance_withdraw.transfer.post_transition'] = ['finance_withdraw_transfer_post_transition'];

        return $events;
    }

    /**
     * This method is called whenever the finance_withdraw.transfer.post_transition event is
     * dispatched.
     *
     * 转账完成后，记账到账户，增加一个笔出项金额，TODO:: 提现手续费
     *
     * @param WorkflowTransitionEvent $event
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function finance_withdraw_transfer_post_transition(WorkflowTransitionEvent $event)
    {
        /** @var Withdraw $withdraw */
        $withdraw = $event->getEntity();

        $this->financeFinanceManager->createLedger(
            $withdraw->getAccount(),
            Ledger::AMOUNT_TYPE_CREDIT,
            $withdraw->getAmount(),
            '提现单 ['.$withdraw->id().'] 提现成功，支出'. $withdraw->getAmount()->getCurrencyCode(). $withdraw->getAmount()->getNumber(),
            $withdraw
        );
    }

}
