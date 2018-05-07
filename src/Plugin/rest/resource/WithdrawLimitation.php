<?php

namespace Drupal\finance\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\finance\Entity\Account;
use Drupal\finance\Entity\AccountType;
use Drupal\finance\FinanceManagerInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "finance_withdraw_limitation",
 *   label = @Translation("Withdraw limitation"),
 *   uri_paths = {
 *     "canonical" = "/api/rest/finance/withdraw-limitation/{finance_account}"
 *   }
 * )
 */
class WithdrawLimitation extends ResourceBase
{

    /**
     * A current user instance.
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $currentUser;

    /**
     * Drupal\finance\FinanceManagerInterface definition.
     *
     * @var \Drupal\finance\FinanceManagerInterface
     */
    protected $financeManager;

    /**
     * Constructs a new WithdrawLimitation object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   A current user instance.
     * @param FinanceManagerInterface $financeManager
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user,
        FinanceManagerInterface $financeManager)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

        $this->currentUser = $current_user;
        $this->financeManager = $financeManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('finance'),
            $container->get('current_user'),
            $container->get('finance.finance_manager')
        );
    }

    /**
     * Responds to GET requests.
     *
     * @param Account $account
     * @return \Drupal\rest\ResourceResponse
     *   The HTTP response object.
     *
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function get(Account $account)
    {

        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }

        $account_type = AccountType::load($account->bundle());

        $response = new ResourceResponse([
            'balance' => [
                'total' => $account->getBalance()->toArray(),
                'available' => $this->financeManager->computeAvailableBalance($account)->toArray()
            ],
            'withdraw' => [
                'min' => $account_type->getMinimumWithdraw(),
                'max' => $account_type->getMaximumWithdraw(),
                'period' => $account_type->getWithdrawPeriod()
            ]
        ], 200);
        $response->addCacheableDependency($account);
        $response->getCacheableMetadata()->setCacheMaxAge(0);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseRoute($canonical_path, $method)
    {
        $route = parent::getBaseRoute($canonical_path, $method);
        $parameters = $route->getOption('parameters') ?: [];
        $parameters['finance_account']['type'] = 'entity:finance_account';
        $route->setOption('parameters', $parameters);

        return $route;
    }
}
