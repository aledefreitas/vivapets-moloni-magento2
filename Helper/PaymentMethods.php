<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\PaymentMethodsEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class PaymentMethods
{
    /**
     * @var string
     */
    const CACHE_TAG = 'Moloni_PaymentMethods';

    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\PaymentMethodsEndpointInterface
     */
    protected $paymentMethodsApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\PaymentMethodsEndpointInterface  $paymentMethodsApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        PaymentMethodsEndpointInterface $paymentMethodsApi
    ) {
        $this->cache = $cache;
        $this->paymentMethodsApi = $paymentMethodsApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  string  $payment_method_name
     *
     * @return int  Moloni's payment_method id
     */
    public function getPaymentMethod(string $payment_method_name)
    {
        $paymentMethods = $this->collectData();

        return $paymentMethods[$payment_method_name] ?? $this->insertPaymentMethod($payment_method_name);
    }

    /**
     * Inserts a payment_method to moloni
     *
     * @param  string  $payment_method_name
     *
     * @return int
     */
    public function insertPaymentMethod(string $payment_method_name)
    {
        $payment_method = $this->paymentMethodsApi->insert(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID,
            $payment_method_name
        );

        $this->cache->remove(self::CACHE_TAG);

        return $payment_method['payment_method_id'] ?: null;
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember(self::CACHE_TAG, function() {
            $availablePaymentMethods = $this->paymentMethodsApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $paymentMethods = [];

            foreach($availablePaymentMethods as $payment_method) {
                $paymentMethods[$payment_method['name']] = $payment_method['payment_method_id'];
            }

            return $paymentMethods;
        });
    }
}
