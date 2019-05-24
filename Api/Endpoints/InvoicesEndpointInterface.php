<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

interface InvoicesEndpointInterface
{
    /**
     * Gets all invoices documents from moloni, for a given company_id
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id);
}
