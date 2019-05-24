<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\CustomersEndpointInterface;

class CustomersEndpoint extends Endpoint implements CustomersEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'customers/';
    }

    /**
     * Gets all customers
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id)
    {
        return $this->send('getAll', [ 'company_id' => $company_id]);
    }

    /**
     * Gets all customers by vat id
     *
     * @param  int  $company_id
     * @param  string  $vat
     *
     * @return mixed
     */
    public function getByVat(int $company_id, string $vat)
    {
        return $this->send('getByVat', [
            'company_id' => $company_id,
            'vat' => $vat,
        ]);
    }

    /**
     * Gets all customers by vat id
     *
     * @param  int  $company_id
     * @param  string  $number  <customer_id>, if guest use <store_id>+<billing_address_id>
     *
     * @return mixed
     */
    public function getByNumber(int $company_id, string $number)
    {

        return $this->send('getByNumber', [
            'company_id' => $company_id,
            'number' => $number,
        ]);
    }

    /**
     * Inserts a customer
     *
     * @param  int  $company_id
     * @param  string  $vat
     * @param  string  $number
     * @param  string  $name
     * @param  int  $language_id
     * @param  string  $address
     * @param  string  $zip_code
     * @param  string  $city
     * @param  int  $country_id
     * @param  int  $maturity_date_id
     * @param  null|int  $payment_method_id
     * @param  null|array  $optionalData
     *
     * @return mixed
     */
    public function insert(
        int $company_id,
        string $vat,
        string $number,
        string $name,
        int $language_id,
        string $address,
        string $zip_code,
        string $city,
        int $country_id,
        int $maturity_date_id,
        ?int $payment_method_id = 1,
        ?array $optionalData = []
    ) {
        $payload = [
            'company_id' => $company_id,
            'vat' => $vat,
            'number' => $number,
            'name' => $name,
            'language_id' => $language_id,
            'address' => $address,
            'zip_code' => $zip_code,
            'city' => $city,
            'country_id' => $country_id,
            'maturity_date_id' => $maturity_date_id,
            'payment_method_id' => $payment_method_id ?? 1,
        ];

        return $this->send('insert', array_merge($payload, $optionalData));
    }

    /**
     * Updates a customer
     *
     * @param  int  $company_id
     * @param  int  $customer
     * @param  string  $vat
     * @param  string  $number
     * @param  string  $name
     * @param  int  $language_id
     * @param  string  $address
     * @param  string  $zip_code
     * @param  string  $city
     * @param  int  $country_id
     * @param  int  $maturity_date_id
     * @param  null|int  $payment_method_id
     * @param  null|array  $optionalData
     *
     * @return mixed
     */
    public function update(
        int $company_id,
        int $customer_id,
        string $vat,
        string $number,
        string $name,
        int $language_id,
        string $address,
        string $zip_code,
        string $city,
        int $country_id,
        int $maturity_date_id,
        ?int $payment_method_id = 1,
        ?array $optionalData = []
    ) {
        $payload = [
            'company_id' => $company_id,
            'customer_id' => $customer_id,
            'vat' => $vat,
            'number' => $number,
            'name' => $name,
            'language_id' => $language_id,
            'address' => $address,
            'zip_code' => $zip_code,
            'city' => $city,
            'country_id' => $country_id,
            'maturity_date_id' => $maturity_date_id,
            'payment_method_id' => $payment_method_id,
        ];

        return $this->send('update', array_merge($payload, $optionalData));
    }
}
