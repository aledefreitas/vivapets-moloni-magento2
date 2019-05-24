<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\TaxesEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class Taxes
{
    /**
     * @var string
     */
    const DEFAULT_EXEMPTION_REASON = 'M05';

    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\TaxesEndpointInterface
     */
    protected $taxesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\TaxesEndpointInterface  $taxesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        TaxesEndpointInterface $taxesApi
    ) {
        $this->cache = $cache;
        $this->taxesApi = $taxesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  int  $tax_value
     * @param  null|string  $country
     *
     * @return int  Moloni's tax id
     */
    public function getTax(int $tax_value, ?string $country = 'PT')
    {
        $taxes = $this->collectData();

        // If destination is outside of Portugal, it's exempt of VAT (therefore, 0% taxes)
        if($country !== 'PT') {
            return $taxes[0];
        }

        return $taxes[round($tax_value, 2)] ?? $taxes[0];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_Taxes', function() {
            $availableTaxes = $this->taxesApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $taxes = [];

            foreach($availableTaxes as $tax) {
                $taxes[round($tax['value'], 2)] = $tax['tax_id'];
            }

            return $taxes;
        });
    }
}
