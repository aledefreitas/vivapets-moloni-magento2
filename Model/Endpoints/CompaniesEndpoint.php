<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\CompaniesEndpointInterface;

class CompaniesEndpoint extends Endpoint implements CompaniesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'companies/';
    }

    /**
     * Gets all companies
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->send('getAll');
    }
}
