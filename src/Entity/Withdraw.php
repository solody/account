<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Withdraw entity.
 *
 * @ingroup finance
 *
 * @ContentEntityType(
 *   id = "finance_withdraw",
 *   label = @Translation("Withdraw"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\WithdrawListBuilder",
 *     "views_data" = "Drupal\finance\Entity\WithdrawViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\finance\Form\WithdrawForm",
 *       "add" = "Drupal\finance\Form\WithdrawForm",
 *       "edit" = "Drupal\finance\Form\WithdrawForm",
 *       "delete" = "Drupal\finance\Form\WithdrawDeleteForm",
 *     },
 *     "access" = "Drupal\finance\WithdrawAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\finance\WithdrawHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "finance_withdraw",
 *   admin_permission = "administer withdraw entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_withdraw/{finance_withdraw}",
 *     "add-form" = "/admin/finance/finance_withdraw/add",
 *     "edit-form" = "/admin/finance/finance_withdraw/{finance_withdraw}/edit",
 *     "delete-form" = "/admin/finance/finance_withdraw/{finance_withdraw}/delete",
 *     "collection" = "/admin/finance/finance_withdraw",
 *   },
 *   field_ui_base_route = "finance_withdraw.settings"
 * )
 */
class Withdraw extends ContentEntityBase implements WithdrawInterface
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
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);


        // 提现账户
        $fields['account_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('所属账户'))
            ->setSetting('target_type', 'finance_account')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label',
                'weight' => 0,
            ]);

        // 提现金额
        $fields['amount'] = BaseFieldDefinition::create('commerce_price')
            ->setLabel(t('提现金额'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'commerce_price_default',
                'weight' => 0,
            ]);

        // 转账方式
        $fields['transfer_method'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('提现方式'))
            ->setSetting('target_type', 'finance_transfer_method')
            ->setSetting('handler', 'default')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label',
                'weight' => 0,
            ]);

        // 处理状态（待审核、正在处理、已拒绝、已完成）(使用状态机)
        $fields['state'] = BaseFieldDefinition::create('state')
            ->setLabel(t('处理状态'))
            ->setDescription(t('待审核、正在处理、已拒绝、已完成'))
            ->setRequired(TRUE)
            ->setSetting('max_length', 255)
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'state_transition_form',
                'weight' => 0,
            ])
            ->setSetting('workflow', 'withdraw_default');


        // 处理人（审核人）
        $fields['auditor_user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('审核人'))
            ->setDescription(t('对提现单进行审核和处理的用户。'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setTranslatable(TRUE)
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label',
                'weight' => 0,
            ]);

        // 审核时间
        $fields['audit_time'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(t('审核时间'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'timestamp',
                'weight' => 0,
            ]);

        // 转账交易号
        $fields['transaction_number'] = BaseFieldDefinition::create('string')
            ->setLabel(t('转账交易号'))
            ->setDescription(t('外部系统转账所产生的交易号，如银行转账，支付宝转账的交易订单号。'))
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string',
                'weight' => 0,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string_textfield',
                'weight' => 0,
            ]);

        // 备注
        $fields['remarks'] = BaseFieldDefinition::create('string_long')
            ->setLabel(t('备注'))
            ->setDescription(t('此提现单的备注说明信息。'))
            ->setSettings([
                'max_length' => 250,
                'text_processing' => 0,
            ])
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'above',
                'type' => 'string',
                'weight' => 0,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string_textarea',
                'weight' => 0,
            ]);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Name'))
            ->setDescription(t('The name of the Withdraw entity.'))
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string'
            ]);

        // 申请时间
        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('申请时间'))
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
