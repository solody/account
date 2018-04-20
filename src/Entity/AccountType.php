<?php

namespace Drupal\finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Account type entity.
 *
 * @ConfigEntityType(
 *   id = "finance_account_type",
 *   label = @Translation("Account type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\finance\AccountTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\finance\Form\AccountTypeForm",
 *       "edit" = "Drupal\finance\Form\AccountTypeForm",
 *       "delete" = "Drupal\finance\Form\AccountTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\finance\AccountTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "finance_account_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "finance_account",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/finance/finance_account_type/{finance_account_type}",
 *     "add-form" = "/admin/finance/finance_account_type/add",
 *     "edit-form" = "/admin/finance/finance_account_type/{finance_account_type}/edit",
 *     "delete-form" = "/admin/finance/finance_account_type/{finance_account_type}/delete",
 *     "collection" = "/admin/finance/finance_account_type"
 *   }
 * )
 */
class AccountType extends ConfigEntityBundleBase implements AccountTypeInterface
{

    /**
     * The Account type ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Account type label.
     *
     * @var string
     */
    protected $label;

    /**
     * 提现周期（天）
     *
     * @var integer
     */
    protected $withdraw_period;

    /**
     * 最小单笔提现限额
     *
     * @var float
     */
    protected $minimum_withdraw;

    /**
     * 最大单笔提现限额
     *
     * @var float
     */
    protected $maximum_withdraw;
}
