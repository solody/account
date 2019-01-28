<?php

namespace Drupal\account\EventSubscriber;

use Drupal\account\Entity\Ledger;
use Drupal\account\Entity\TransferGatewayInterface;
use Drupal\account\Entity\TransferMethodInterface;
use Drupal\account\Entity\Withdraw;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\account\FinanceManagerInterface;

/**
 * Class WithdrawSubscriber.
 */
class WithdrawSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\account\FinanceManagerInterface definition.
   *
   * @var \Drupal\account\FinanceManagerInterface
   */
  protected $accountFinanceManager;

  /**
   * Constructs a new WithdrawSubscriber object.
   */
  public function __construct(FinanceManagerInterface $account_finance_manager) {
    $this->accountFinanceManager = $account_finance_manager;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['withdraw.transfer.pre_transition'] = ['withdraw_transfer_pre_transition'];
    $events['withdraw.transfer.post_transition'] = ['withdraw_transfer_post_transition'];
    $events['withdraw.cancel.post_transition'] = ['withdraw_cancel_post_transition'];

    return $events;
  }

  /**
   * This method is called whenever the withdraw.transfer.pre_transition event is
   * dispatched.
   *
   * 状态切换之前，执行转账插件
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Exception
   */
  public function withdraw_transfer_pre_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();

    $transfer_method = $withdraw->getTransferMethod();
    if ($transfer_method instanceof TransferMethodInterface) {
      $gateway = $transfer_method->getTransferGateway();
      if ($gateway instanceof TransferGatewayInterface) {
        $plugin = $gateway->getPlugin();
        if ($plugin instanceof \Drupal\account\Plugin\TransferGatewayInterface) {
          try {
            $plugin->transfer($withdraw);
            \Drupal::messenger()->addMessage('提现单['.$withdraw->id().']状态已切换为[已完成]，'.$plugin->getPluginId().'转帐打款请求成功：');
          } catch (\Exception $exception) {
            \Drupal::messenger()->addError('提现单['.$withdraw->id().']打款失败：' . $plugin->getPluginId().':'.$exception->getMessage());
            // 跳转以终止状态转换
            header('Location: '.$_SERVER['REQUEST_URI']);
            exit();
          }
        }
      }
    }
  }

  /**
   * This method is called whenever the withdraw.transfer.post_transition event is
   * dispatched.
   *
   * TODO:: 提现手续费
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function withdraw_transfer_post_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();
  }

  /**
   * This method is called whenever the withdraw.cancel.post_transition event is
   * dispatched.
   *
   * 退款到账户余额
   *
   * @param WorkflowTransitionEvent $event
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function withdraw_cancel_post_transition(WorkflowTransitionEvent $event) {
    /** @var Withdraw $withdraw */
    $withdraw = $event->getEntity();

    $this->accountFinanceManager->createLedger(
      $withdraw->getAccount(),
      Ledger::AMOUNT_TYPE_DEBIT,
      $withdraw->getAmount(),
      '提现单 [' . $withdraw->id() . '] 被拒绝，金额退回' . $withdraw->getAmount()->getCurrencyCode() . $withdraw->getAmount()->getNumber(),
      $withdraw
    );
  }
}
