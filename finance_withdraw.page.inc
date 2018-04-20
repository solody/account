<?php

/**
 * @file
 * Contains finance_withdraw.page.inc.
 *
 * Page callback for Withdraw entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Withdraw templates.
 *
 * Default template: finance_withdraw.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_finance_withdraw(array &$variables) {
  // Fetch Withdraw Entity Object.
  $finance_withdraw = $variables['elements']['#finance_withdraw'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
