<?php

namespace Drupal\finance\Form;

use CommerceGuys\Intl\Formatter\CurrencyFormatterInterface;
use Drupal\commerce_price\Price;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\finance\Entity\Account;
use Drupal\finance\Entity\AccountType;
use Drupal\finance\Entity\TransferMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\finance\FinanceManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ApplyWithdrawForm.
 */
class ApplyWithdrawForm extends FormBase {

  /**
   * Drupal\finance\FinanceManagerInterface definition.
   *
   * @var \Drupal\finance\FinanceManagerInterface
   */
  protected $financeFinanceManager;

  /**
   * The currency formatter.
   *
   * @var \CommerceGuys\Intl\Formatter\CurrencyFormatterInterface
   */
  protected $currencyFormatter;

  /**
   * Constructs a new ApplyWithdrawForm object.
   */
  public function __construct(
    FinanceManagerInterface $finance_finance_manager,
    CurrencyFormatterInterface $currency_formatter
  ) {
    $this->financeFinanceManager = $finance_finance_manager;
    $this->currencyFormatter = $currency_formatter;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('finance.finance_manager'),
      $container->get('commerce_price.currency_formatter')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'finance_apply_withdraw_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $finance_account_id = $this->getRouteMatch()->getParameter('finance_account');
    $finance_account = Account::load($finance_account_id);

    if ($finance_account->getOwnerId() !== $this->currentUser()->id()) {
      throw new AccessDeniedHttpException('您无权访问此账户');
    }

    $finance_account_type = AccountType::load($finance_account->bundle());
    $available_balance = $this->financeFinanceManager->computeAvailableBalance($finance_account);
    $withdraw_limitation = '没有限制';
    if ($finance_account_type->getMinimumWithdraw() || $finance_account_type->getMaximumWithdraw()) {
      $withdraw_limitation = '';
      if ($finance_account_type->getMinimumWithdraw()) {
        $price = $this->currencyFormatter->format((string)$finance_account_type->getMinimumWithdraw(), 'CNY');
        $withdraw_limitation .= '<div>最低限制：'.$price.'</div>';
      }
      if ($finance_account_type->getMaximumWithdraw()) {
        $price = $this->currencyFormatter->format((string)$finance_account_type->getMaximumWithdraw(), 'CNY');
        $withdraw_limitation .= '<div>最高限制：'.$price.'</div>';
      }
    }

    // 显示账户摘要：余额，可提余额，提现限制
    $form['account_summary'] = [
      '#type' => 'table',
      '#caption' => $finance_account->getName(),
      '#header' => [
        $this->t('余额'),
        $this->t('可提余额'),
        $this->t('提现限制')
      ]
    ];
    $form['account_summary'][] = [
      ['#markup' => $this->currencyFormatter->format($finance_account->getBalance()->getNumber(), $finance_account->getBalance()->getCurrencyCode())],
      ['#markup' => $this->currencyFormatter->format($available_balance->getNumber(), $available_balance->getCurrencyCode())],
      ['#markup' => $withdraw_limitation]
    ];

    $form['withdraw'] = [
      '#type' => 'fieldset',
      '#tree' => true,
      '#title' => $this->t('申请提现')
    ];

    // 输入要提现的金额
    $form['withdraw']['amount'] = [
      '#type' => 'commerce_price',
      '#title' => $this->t('输入要提现的金额'),
      '#default_value' => ['number' => '100.00', 'currency_code' => 'CNY'],
      '#allow_negative' => FALSE,
      '#size' => 30,
      '#maxlength' => 128,
      '#required' => TRUE,
      '#available_currencies' => ['CNY'],
    ];
    
    // 选择提现方式
    $transfer_methods = \Drupal::entityTypeManager()
      ->getStorage('finance_transfer_method')
      ->loadByProperties(['user_id' => $this->currentUser()->id()]);

    $transfer_method_options = [];
    $default_transfer_method = null;
    foreach ($transfer_methods as $transfer_method) {
      if ($transfer_method instanceof TransferMethod) {
        if (!$default_transfer_method) $default_transfer_method = $transfer_method->id();
        $transfer_method_options[$transfer_method->id()] = $transfer_method->getName();
      }
    }

    $form['withdraw']['transfer_method'] = [
      '#type' => 'select',
      '#title' => $this->t('提现方式'),
      '#default_value' => $default_transfer_method,
      '#options' => $transfer_method_options,
      '#required' => TRUE
    ];

    $form['withdraw']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // 检查金额
    $finance_account_id = $this->getRouteMatch()->getParameter('finance_account');
    $finance_account = Account::load($finance_account_id);
    $finance_account_type = AccountType::load($finance_account->bundle());
    $available_balance = $this->financeFinanceManager->computeAvailableBalance($finance_account);

    $amount = $form_state->getValue('withdraw')['amount'];
    $amount_price = new Price($amount['number'], $amount['currency_code']);
    if ($amount_price->greaterThan($available_balance)) {
      $form_state->setError($form['withdraw']['amount'], '超过了可提现金额');
    }
    if ($finance_account_type->getMinimumWithdraw() &&
      (float)$amount_price->getNumber() < (float)$finance_account_type->getMinimumWithdraw()) {
      $form_state->setError($form['withdraw']['amount'], '没到达到最小提现限额（'.$this->currencyFormatter->format($finance_account_type->getMinimumWithdraw(), 'CNY').'）');
    }
    if ($finance_account_type->getMaximumWithdraw() &&
      (float)$amount_price->getNumber() > (float)$finance_account_type->getMaximumWithdraw()) {
      $form_state->setError($form['withdraw']['amount'], '超过了最大提现限额（'.$this->currencyFormatter->format($finance_account_type->getMaximumWithdraw(), 'CNY').'）');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // 创建提现单
    $transfer_method = TransferMethod::load($form_state->getValue('withdraw')['transfer_method']);
    if (!$transfer_method) throw new BadRequestHttpException('找不到支付方法：【'.$form_state->getValue('withdraw')['transfer_method'].'】');

    try {
      $finance_account_id = $this->getRouteMatch()->getParameter('finance_account');
      $finance_account = Account::load($finance_account_id);
      $amount = $form_state->getValue('withdraw')['amount'];
      $amount_price = new Price($amount['number'], $amount['currency_code']);

      $withdraw = $this->financeFinanceManager->applyWithdraw($finance_account, $amount_price, $transfer_method, '商家提现');

      \Drupal::messenger()->addMessage('提现申请成功！');
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($e->getMessage());
    }
  }

}
