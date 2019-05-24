<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

interface PaymentMethodsEndpointInterface
{
    /**
     * Gets all product categories
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id);

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
    );
}
