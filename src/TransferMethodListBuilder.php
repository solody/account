<?php

namespace Drupal\finance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Transfer method entities.
 *
 * @ingroup finance
 */
class TransferMethodListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Transfer method ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\finance\Entity\TransferMethod */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.finance_transfer_method.edit_form',
      ['finance_transfer_method' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
