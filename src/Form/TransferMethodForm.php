<?php

namespace Drupal\finance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Transfer method edit forms.
 *
 * @ingroup finance
 */
class TransferMethodForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\finance\Entity\TransferMethod */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Transfer method.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Transfer method.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.finance_transfer_method.canonical', ['finance_transfer_method' => $entity->id()]);
  }

}
