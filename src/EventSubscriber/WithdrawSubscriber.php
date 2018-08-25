<?php

namespace Drupal\finance\EventSubscriber;

use Drupal\finance\Entity\Ledger;
use Drupal\finance\Entity\TransferGatewayInterface;
use Drupal\finance\Entity\TransferMethodInterface;
use Drupal\finance\Entity\Withdraw;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\finance\FinanceManagerInterface;

/**
 * Class WithdrawSubscriber.
 */
class WithdrawSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\finance\FinanceManagerInterface definition.
   *
   * @var \Drupal\finance\FinanceManagerInterface
   */
  protected $financeFinanceManager;

  /**
   * Constructs a new WithdrawSubscriber object.
   */
  public function __construct(FinanceManagerInterface $finance_finance_manager) {
    $this->financeFinanceManager = $finance_finance_manager;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['finance_withdraw.transfer.pre_transition'] = ['finance_withdraw_transfer_pre_transition'];
    $events['finance_withdraw.transfer.post_transition'] = ['finance_withdraw_transfer_post_transition'];
    $events['finance_withdraw.cancel.post_transition'] = ['finance_withdraw_cancel_post_transition'];

    return $events;
  }

  /**
   * This method is called whenever the finance_withdraw.transfer.pre_transition event is
   * dispatched.
   *
   * 状态切换之前，执行转账插件
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Exception
   */
  public function finance_withdraw_transfer_pre_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();

    $transfer_method = $withdraw->getTransferMethod();
    if ($transfer_method instanceof TransferMethodInterface) {
      $gateway = $transfer_method->getTransferGateway();
      if ($gateway instanceof TransferGatewayInterface) {
        $plugin = $gateway->getPlugin();
        if ($plugin instanceof \Drupal\finance\Plugin\TransferGatewayInterface) {
          try {
            $plugin->transfer($withdraw);
          } catch (\Exception $exception) {
            \Drupal::messenger()->addWarning('提现单['.$withdraw->id().']状态已切换为[已完成]，但自动打款失败：' . $plugin->getPluginId().':'.$exception->getMessage());
          }
        }
      }
    }
  }

  /**
   * This method is called whenever the finance_withdraw.transfer.post_transition event is
   * dispatched.
   *
   * TODO:: 提现手续费
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function finance_withdraw_transfer_post_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();
  }

  /**
   * This method is called whenever the finance_withdraw.cancel.post_transition event is
   * dispatched.
   *
   * 退款到账户余额
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function finance_withdraw_cancel_post_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();

    $this->financeFinanceManager->createLedger(
      $withdraw->getAccount(),
      Ledger::AMOUNT_TYPE_DEBIT,
      $withdraw->getAmount(),
      '提现单 [' . $withdraw->id() . '] 被拒绝，金额退回' . $withdraw->getAmount()->getCurrencyCode() . $withdraw->getAmount()->getNumber(),
      $withdraw
    );
  }
}
