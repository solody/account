<?php

/**
 * @file
 * Contains finance_account.page.inc.
 *
 * Page callback for Account entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Account templates.
 *
 * Default template: finance_account.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_finance_account(array &$variables) {
  // Fetch Account Entity Object.
  $finance_account = $variables['elements']['#finance_account'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
