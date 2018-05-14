<?php

namespace Drupal\finance\Entity;

use Drupal\commerce_price\Price;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Account entity.
 *
 * @ingroup finance
 *
 * @ContentEntityType(
 *   id = "finance_account",
 *   label = @Translation("Account"),
 *   bundle_label = @Translation("Account type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\AccountListBuilder",
 *     "views_data" = "Drupal\finance\Entity\AccountViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\finance\Form\AccountForm",
 *       "add" = "Drupal\finance\Form\AccountForm",
 *       "edit" = "Drupal\finance\Form\AccountForm",
 *       "delete" = "Drupal\finance\Form\AccountDeleteForm",
 *     },
 *     "access" = "Drupal\finance\AccountAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\finance\AccountHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "finance_account",
 *   admin_permission = "administer account entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_account/{finance_account}",
 *     "add-page" = "/admin/finance/finance_account/add",
 *     "add-form" = "/admin/finance/finance_account/add/{finance_account_type}",
 *     "edit-form" = "/admin/finance/finance_account/{finance_account}/edit",
 *     "delete-form" = "/admin/finance/finance_account/{finance_account}/delete",
 *     "collection" = "/admin/finance/finance_account",
 *   },
 *   bundle_entity_type = "finance_account_type",
 *   field_ui_base_route = "entity.finance_account_type.edit_form"
 * )
 */
class Account extends ContentEntityBase implements AccountInterface
{

    use EntityChangedTrait;

    /**
     * {@inheritdoc}
     */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
    {
        parent::preCreate($storage_controller, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->get('name')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->get('created')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($timestamp)
    {
        $this->set('created', $timestamp);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->get('user_id')->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId()
    {
        return $this->get('user_id')->target_id;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($uid)
    {
        $this->set('user_id', $uid);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(UserInterface $account)
    {
        $this->set('user_id', $account->id());
        return $this;
    }

    /**
     * @return Price
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function getBalance()
    {
        if (!$this->get('balance')->isEmpty()) {
            return $this->get('balance')->first()->toPrice();
        }
    }

    /**
     * @param Price $amount
     * @return $this
     */
    public function setBalance(Price $amount)
    {
        $this->set('balance', $amount);
        return $this;
    }

    /**
     * @return Price
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function getTotalCredit()
    {
        if (!$this->get('total_credit')->isEmpty()) {
            return $this->get('total_credit')->first()->toPrice();
        }
    }

    /**
     * @param Price $amount
     * @return $this
     */
    public function setTotalCredit(Price $amount)
    {
        $this->set('total_credit', $amount);
        return $this;
    }

    /**
     * @return Price
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function getTotalDebit()
    {
        if (!$this->get('total_debit')->isEmpty()) {
            return $this->get('total_debit')->first()->toPrice();
        }
    }

    /**
     * @param Price $amount
     * @return $this
     */
    public function setTotalDebit(Price $amount)
    {
        $this->set('total_debit', $amount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        // 账户所属用户
        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('账户所属用户'))
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label',
                'weight' => 0,
            ]);

        // 账户名称
        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('账户名称'))
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => 0,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string_textfield',
                'weight' => 0,
            ]);

        // 账户进项累计（借记）
        $fields['total_debit'] = BaseFieldDefinition::create('commerce_price')
            ->setLabel(t('账户进项累计'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'commerce_price_default',
                'weight' => 0,
            ])
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        // 账户出项累计（贷记）
        $fields['total_credit'] = BaseFieldDefinition::create('commerce_price')
            ->setLabel(t('账户出项累计'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'commerce_price_default',
                'weight' => 0,
            ]);

        // 账户余额
        $fields['balance'] = BaseFieldDefinition::create('commerce_price')
            ->setLabel(t('账户余额'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'commerce_price_default',
                'weight' => 0,
            ]);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('创建时间'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'timestamp',
                'weight' => 0,
            ]);

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('更新时间'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'timestamp',
                'weight' => 0,
            ]);

        return $fields;
    }

}
