<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\MaturityDatesEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class MaturityDates
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\MaturityDatesEndpointInterface
     */
    protected $maturityDatesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\MaturityDatesEndpointInterface  $maturityDatesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        MaturityDatesEndpointInterface $maturityDatesApi
    ) {
        $this->cache = $cache;
        $this->maturityDatesApi = $maturityDatesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @return int  Moloni's maturityDate id
     */
    public function getMaturityDate()
    {
        $maturityDates = $this->collectData();

        return $maturityDates[0];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_MaturityDates', function() {
            $availableMaturityDates = $this->maturityDatesApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $maturityDates = [];

            foreach($availableMaturityDates as $maturityDate) {
                $maturityDates[$maturityDate['days']] = $maturityDate['maturity_date_id'];
            }

            return $maturityDates;
        });
    }
}
