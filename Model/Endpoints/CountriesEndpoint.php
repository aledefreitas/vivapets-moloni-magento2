<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\CountriesEndpointInterface;

class CountriesEndpoint extends Endpoint implements CountriesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'countries/';
    }

    /**
     * Gets all countries
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->send('getAll');
    }
}
