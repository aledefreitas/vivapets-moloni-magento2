<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\CountriesEndpointInterface;

class Countries
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\CountriesEndpointInterface
     */
    protected $countriesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\CountriesEndpointInterface  $countriesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        CountriesEndpointInterface $countriesApi
    ) {
        $this->cache = $cache;
        $this->countriesApi = $countriesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  string  $country_code
     *
     * @return int  Moloni's country id
     */
    public function getCountry(string $country_code)
    {
        $countries = $this->collectData();

        return $countries[strtolower($country_code)] ?? $countries['pt'];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_Countries', function() {
            $availableCountries = $this->countriesApi->getAll();

            $countries = [];

            foreach($availableCountries as $country) {
                // Checks if country already exists
                // This is needed to prevent US showing up as Hawai, since iso_3166_1 is not unique in Moloni
                if(!isset($countries[strtolower($country['iso_3166_1'])])) {
                    $countries[strtolower($country['iso_3166_1'])] = $country['country_id'];
                }
            }

            return $countries;
        });
    }
}
