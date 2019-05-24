<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\CurrenciesEndpointInterface;

class Currencies
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\CurrenciesEndpointInterface
     */
    protected $currenciesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\CurrenciesEndpointInterface  $currenciesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        CurrenciesEndpointInterface $currenciesApi
    ) {
        $this->cache = $cache;
        $this->currenciesApi = $currenciesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  string  $currency_code
     *
     * @return null|int  Moloni's currency id
     */
    public function getCurrency(string $currency_code)
    {
        $currencies = $this->collectData();

        return $currencies[$currency_code] ?? null;
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_Currencies', function() {
            $availableCurrencies = $this->currenciesApi->getAll();

            $currencies = [];

            foreach($availableCurrencies as $currency) {
                $currencies[$currency['iso4217']] = $currency['currency_id'];
            }

            return $currencies;
        });
    }
}
