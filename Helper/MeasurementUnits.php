<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\MeasurementUnitsEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class MeasurementUnits
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\MeasurementUnitsEndpointInterface
     */
    protected $measurementUnitsApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\MeasurementUnitsEndpointInterface  $measurementUnitsApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        MeasurementUnitsEndpointInterface $measurementUnitsApi
    ) {
        $this->cache = $cache;
        $this->measurementUnitsApi = $measurementUnitsApi;
    }

    /**
     * Gets data from moloni api
     *
     * @return int  Moloni's measurementUnit id
     */
    public function getMeasurementUnit()
    {
        $measurementUnits = $this->collectData();
        
        return $measurementUnits['Uni.'];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_MeasurementUnits', function() {
            $availableMeasurementUnits = $this->measurementUnitsApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $measurementUnits = [];

            foreach($availableMeasurementUnits as $measurementUnit) {
                $measurementUnits[$measurementUnit['short_name']] = $measurementUnit['unit_id'];
            }

            return $measurementUnits;
        });
    }
}
