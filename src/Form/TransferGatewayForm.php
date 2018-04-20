<?php

namespace Drupal\finance\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TransferGatewayForm.
 */
class TransferGatewayForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $finance_transfer_gateway = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $finance_transfer_gateway->label(),
      '#description' => $this->t("Label for the Transfer gateway."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $finance_transfer_gateway->id(),
      '#machine_name' => [
        'exists' => '\Drupal\finance\Entity\TransferGateway::load',
      ],
      '#disabled' => !$finance_transfer_gateway->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $finance_transfer_gateway = $this->entity;
    $status = $finance_transfer_gateway->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Transfer gateway.', [
          '%label' => $finance_transfer_gateway->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Transfer gateway.', [
          '%label' => $finance_transfer_gateway->label(),
        ]));
    }
    $form_state->setRedirectUrl($finance_transfer_gateway->toUrl('collection'));
  }

}
