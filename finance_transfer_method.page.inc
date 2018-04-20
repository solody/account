<?php

/**
 * @file
 * Contains finance_transfer_method.page.inc.
 *
 * Page callback for Transfer method entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Transfer method templates.
 *
 * Default template: finance_transfer_method.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_finance_transfer_method(array &$variables) {
  // Fetch TransferMethod Entity Object.
  $finance_transfer_method = $variables['elements']['#finance_transfer_method'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
