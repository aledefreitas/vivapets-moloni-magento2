<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\CurrenciesEndpointInterface;

class CurrenciesEndpoint extends Endpoint implements CurrenciesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'currencies/';
    }

    /**
     * Gets all currencies
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->send('getAll');
    }
}
