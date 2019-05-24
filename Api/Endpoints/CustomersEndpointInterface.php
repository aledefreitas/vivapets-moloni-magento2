<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

interface CustomersEndpointInterface
{
    /**
     * @var string
     */
    const DEFAULT_VAT_ID = '999999990';

    /**
     * Gets all customers
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id);

    /**
     * Gets all customers by vat id
     *
     * @param  int  $company_id
     * @param  string  $vat
     *
     * @return mixed
     */
    public function getByVat(int $company_id, string $vat);

    /**
     * Gets all customers by vat id
     *
     * @param  int  $company_id
     * @param  string  $number  <customer_id>, if guest use <store_id>+<billing_address_id>
     *
     * @return mixed
     */
    public function getByNumber(int $company_id, string $number);

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
    );

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
    );
}
