<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Ledger entity.
 *
 * @ingroup finance
 *
 * @ContentEntityType(
 *   id = "finance_ledger",
 *   label = @Translation("Ledger"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\LedgerListBuilder",
 *     "views_data" = "Drupal\finance\Entity\LedgerViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\finance\Form\LedgerForm",
 *       "add" = "Drupal\finance\Form\LedgerForm",
 *       "edit" = "Drupal\finance\Form\LedgerForm",
 *       "delete" = "Drupal\finance\Form\LedgerDeleteForm",
 *     },
 *     "access" = "Drupal\finance\LedgerAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\finance\LedgerHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "finance_ledger",
 *   admin_permission = "administer ledger entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "remarks",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_ledger/{finance_ledger}",
 *     "add-form" = "/admin/finance/finance_ledger/add",
 *     "edit-form" = "/admin/finance/finance_ledger/{finance_ledger}/edit",
 *     "delete-form" = "/admin/finance/finance_ledger/{finance_ledger}/delete",
 *     "collection" = "/admin/finance/finance_ledger",
 *   },
 *   field_ui_base_route = "finance_ledger.settings"
 * )
 */
class Ledger extends ContentEntityBase implements LedgerInterface
{
    const AMOUNT_TYPE_DEBIT = 'debit';  // 借记，进项
    const AMOUNT_TYPE_CREDIT = 'credit'; // 贷记，出项

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

        // 所属账户
        $fields['account_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('所属账户'))
            ->setSetting('target_type', 'finance_account')
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
