<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Entities;

use Vivapets\Moloni\Api\Entities\TaxesEntityInterface;

interface TaxesCollectionEntityInterface
{
    /**
     * Adds a payment to collection
     *
     * @param  TaxEntityInterface  $payment
     */
    public function addTax(TaxEntityInterface $payment);

    /**
     * Gets all payments
     *
     * @return TaxEntityInterface[]
     */
    public function getTaxes() : array;
}
