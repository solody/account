<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Transfer method entity.
 *
 * @ingroup finance
 *
 * @ContentEntityType(
 *   id = "finance_transfer_method",
 *   label = @Translation("Transfer method"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\TransferMethodListBuilder",
 *     "views_data" = "Drupal\finance\Entity\TransferMethodViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\finance\Form\TransferMethodForm",
 *       "add" = "Drupal\finance\Form\TransferMethodForm",
 *       "edit" = "Drupal\finance\Form\TransferMethodForm",
 *       "delete" = "Drupal\finance\Form\TransferMethodDeleteForm",
 *     },
 *     "access" = "Drupal\finance\TransferMethodAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\finance\TransferMethodHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "finance_transfer_method",
 *   admin_permission = "administer transfer method entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "bundle" = "type",
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_transfer_method/{finance_transfer_method}",
 *     "add-form" = "/admin/finance/finance_transfer_method/add",
 *     "edit-form" = "/admin/finance/finance_transfer_method/{finance_transfer_method}/edit",
 *     "delete-form" = "/admin/finance/finance_transfer_method/{finance_transfer_method}/delete",
 *     "collection" = "/admin/finance/finance_transfer_method",
 *   },
 *   field_ui_base_route = "finance_transfer_method.settings",
 *   bundle_label = @Translation("Transfer method type"),
 *   bundle_plugin_type = "transfer_gateway"
 * )
 */
class TransferMethod extends ContentEntityBase implements TransferMethodInterface
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
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('所属用户'))
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label'
            ]);

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('提现方式名称'))
            ->setDefaultValue('')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'string'
            ])
            ->setDisplayOptions('form', [
                'type' => 'string_textfield'
            ]);

        $fields['transfer_gateway'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Transfer gateway'))
            ->setDescription(t('The transfer gateway.'))
            ->setRequired(TRUE)
            ->setSetting('target_type', 'finance_transfer_gateway')
            ->setDisplayOptions('view', [
                'label' => 'inline',
                'type' => 'entity_reference_label'
            ]);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }

}
