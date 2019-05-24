<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\TaxesEndpointInterface;

class TaxesEndpoint extends Endpoint implements TaxesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'taxes/';
    }

    /**
     * Gets all taxes
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id)
    {
        return $this->send('getAll', [ 'company_id' => $company_id ]);
    }
}
