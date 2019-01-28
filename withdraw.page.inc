<?php

/**
 * @file
 * Contains withdraw.page.inc.
 *
 * Page callback for Withdraw entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Withdraw templates.
 *
 * Default template: withdraw.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_withdraw(array &$variables) {
  // Fetch Withdraw Entity Object.
  $withdraw = $variables['elements']['#withdraw'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
