<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Entities;

use Vivapets\Moloni\Api\Entities\PaymentEntityInterface;

interface PaymentCollectionEntityInterface
{
    /**
     * Adds a payment to collection
     *
     * @param  PaymentEntityInterface  $payment
     */
    public function addPayment(PaymentEntityInterface $payment);

    /**
     * Gets all payments
     *
     * @return PaymentEntityInterface[]
     */
    public function getPayments() : array;
}
