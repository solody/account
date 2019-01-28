<?php

namespace Drupal\account\Plugin\rest\resource;

use Drupal\commerce_price\Price;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\account\Entity\Account;
use Drupal\account\Entity\TransferMethod;
use Drupal\account\FinanceManagerInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "account_apply_withdraw",
 *   label = @Translation("Apply withdraw"),
 *   uri_paths = {
 *     "create" = "/api/rest/account/apply-withdraw/{account}"
 *   }
 * )
 */
class ApplyWithdraw extends ResourceBase
{

    /**
     * A current user instance.
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $currentUser;

    /**
     * Drupal\account\FinanceManagerInterface definition.
     *
     * @var \Drupal\account\FinanceManagerInterface
     */
    protected $financeManager;

    /**
     * Constructs a new ApplyWithdraw object.
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
            $container->get('logger.factory')->get('account'),
            $container->get('current_user'),
            $container->get('account.finance_manager')
        );
    }

    /**
     * Responds to POST requests.
     *
     * @param Account $account
     * @return \Drupal\rest\ModifiedResourceResponse
     *   The HTTP response object.
     */
    public function post(Account $account, $data)
    {

        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }

        $transfer_method = TransferMethod::load($data['transfer_method']);
        if (!$transfer_method) throw new BadRequestHttpException('找不到支付方法：【'.$data['transfer_method'].'】');

        try {
            $withdraw = $this->financeManager->applyWithdraw($account, new Price($data['amount'], 'CNY'), $transfer_method, $data['remarks']);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new ModifiedResourceResponse($withdraw, 200);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseRoute($canonical_path, $method)
    {
        $route = parent::getBaseRoute($canonical_path, $method);
        $parameters = $route->getOption('parameters') ?: [];
        $parameters['account']['type'] = 'entity:account';
        $route->setOption('parameters', $parameters);

        return $route;
    }
}
