<?php

namespace Drupal\account\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Ledger entity.
 *
 * @ingroup account
 *
 * @ContentEntityType(
 *   id = "ledger",
 *   label = @Translation("Ledger"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\account\LedgerListBuilder",
 *     "views_data" = "Drupal\account\Entity\LedgerViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\account\Form\LedgerForm",
 *       "add" = "Drupal\account\Form\LedgerForm",
 *       "edit" = "Drupal\account\Form\LedgerForm",
 *       "delete" = "Drupal\account\Form\LedgerDeleteForm",
 *     },
 *     "access" = "Drupal\account\LedgerAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\account\LedgerHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "ledger",
 *   admin_permission = "administer ledger entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "remarks",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/account/ledger/{ledger}",
 *     "add-form" = "/admin/account/ledger/add",
 *     "edit-form" = "/admin/account/ledger/{ledger}/edit",
 *     "delete-form" = "/admin/account/ledger/{ledger}/delete",
 *     "collection" = "/admin/account/ledger",
 *   },
 *   field_ui_base_route = "ledger.settings"
 * )
 */
class Ledger extends ContentEntityBase implements LedgerInterface {
  const AMOUNT_TYPE_DEBIT = 'debit';  // 借记，进项
  const AMOUNT_TYPE_CREDIT = 'credit'; // 贷记，出项

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getBalance() {
    if (!$this->get('balance')->isEmpty()) {
      return $this->get('balance')->first()->toPrice();
    }
  }

  /**
   * @return string
   */
  public function getAmountType() {
    return $this->get('amount_type')->value;
  }

  /**
   * @inheritdoc
   */
  public function getAmount() {
    if (!$this->get('amount')->isEmpty()) {
      return $this->get('amount')->first()->toPrice();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount() {
    return $this->get('account_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountId() {
    return $this->get('account_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountType() {
    return $this->getAccount()->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // 所属账户
    $fields['account_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('所属账户'))
      ->setSetting('target_type', 'account')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => 0,
      ]);

    // 记账类型（进/出）
    $fields['amount_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('记账类型'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ]);

    // 记账金额
    $fields['amount'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('记账金额'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'commerce_price_default',
        'weight' => 0,
      ]);

    // 记账余额
    $fields['balance'] = BaseFieldDefinition::create('commerce_price')
      ->setLabel(t('记账余额'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'commerce_price_default',
        'weight' => 0,
      ]);

    // 备注
    $fields['remarks'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('备注'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ]);

    $fields['source'] = BaseFieldDefinition::create('dynamic_entity_reference')
      ->setLabel(t('记账来源'))
      ->setDisplayOptions('view', [
        'type' => 'dynamic_entity_reference_label'
      ]);

    // 发生时间
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('发生时间'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 0,
      ]);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
