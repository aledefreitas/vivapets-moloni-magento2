<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

interface TaxesEndpointInterface
{
    /**
     * Gets all taxes
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id);
}
