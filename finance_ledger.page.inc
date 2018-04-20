<?php

/**
 * @file
 * Contains finance_ledger.page.inc.
 *
 * Page callback for Ledger entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Ledger templates.
 *
 * Default template: finance_ledger.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_finance_ledger(array &$variables) {
  // Fetch Ledger Entity Object.
  $finance_ledger = $variables['elements']['#finance_ledger'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
