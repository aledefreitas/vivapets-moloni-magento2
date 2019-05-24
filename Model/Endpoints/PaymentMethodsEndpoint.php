<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\PaymentMethodsEndpointInterface;

class PaymentMethodsEndpoint extends Endpoint implements PaymentMethodsEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'paymentMethods/';
    }

    /**
     * Gets all payment methods
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id)
    {
        return $this->send('getAll', [
            'company_id' => $company_id
        ]);
    }

    /**
     * Inserts a payment method
     *
     * @param  int  $company_id
     * @param  string  $name
     * @param  bool  $is_numerary
     *
     * @return mixed
     */
    public function insert(
        int $company_id,
        string $name,
        bool $is_numerary = false
    ) {
        return $this->send('insert', [
            'company_id' => $company_id,
            'name' => $name,
            'is_numerary' => $is_numerary ? 1 : 0,
        ]);
    }
}
