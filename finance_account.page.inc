<?php

/**
 * @file
 * Contains finance_account.page.inc.
 *
 * Page callback for Account entities.
 */

use Drupal\Core\Datetime\DrupalDateTime;
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
function template_preprocess_finance_account(array &$variables)
{
    // Fetch Account Entity Object.
    /** @var \Drupal\finance\Entity\AccountInterface $finance_account */
    $finance_account = $variables['elements']['#finance_account'];

    // Helpful $content variable for templates.
    foreach (Element::children($variables['elements']) as $key) {
        $variables['content'][$key] = $variables['elements'][$key];
    }

    // 账户标题
    /** @var \Drupal\user\Entity\User $user */
    $user = $finance_account->getOwner();
    /** @var \Drupal\finance\Entity\AccountType $finance_account_type */
    $finance_account_type = $finance_account->get('type')->entity;
    $variables['title'] = $user->getAccountName() . ' 的 ' . $finance_account_type->getLabel();

    // 账户明细按钮
    $variables['detail_link'] = [
        '#title' => t('查看账户流水'),
        '#type' => 'link',
        '#url' => \Drupal\Core\Url::fromRoute('view.finance_ledgers.page_1', ['finance_account_id' => $finance_account->id()]),
    ];

    // 账户统计表
    $table = array(
        '#type' => 'table',
        '#caption' => t('账户统计表'),
        '#header' => array(
            t('项目'),
            t('金额'),
        ),
    );

    $profit_data = [
        [
            'name' => t('进项累计'),
            'amount' => $variables['elements']['total_debit'][0]['#markup']
        ],
        [
            'name' => t('出项累计'),
            'amount' => $variables['elements']['total_credit'][0]['#markup']
        ],
        [
            'name' => t('余额'),
            'amount' => $variables['elements']['balance'][0]['#markup']
        ]
    ];

    foreach ($profit_data as $i => $data) {
        $table[$i]['#attributes'] = [
            'class' => [
                'foo',
                'baz',
            ],
        ];

        $table[$i]['name'] = [
            '#markup' => $data['name'],
        ];
        $table[$i]['amount'] = [
            '#markup' => $data['amount'],
        ];
    }

    $variables['table'] = $table;
}
