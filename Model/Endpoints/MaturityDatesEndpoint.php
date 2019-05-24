<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\MaturityDatesEndpointInterface;

class MaturityDatesEndpoint extends Endpoint implements MaturityDatesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'maturityDates/';
    }

    /**
     * Gets all maturity dates
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
