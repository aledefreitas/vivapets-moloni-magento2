<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\PaymentCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\PaymentEntityInterface;

class PaymentCollectionEntity implements PaymentCollectionEntityInterface
{
    /**
     * @var PaymentEntityInterface[]
     */
    protected $payments = [];

    /**
     * Adds a payment to collection
     *
     * @param  PaymentEntityInterface  $payment
     *
     * @return void
     */
    public function addPayment(PaymentEntityInterface $payment)
    {
        $this->payments[] = $payment;
    }

    /**
     * Gets all payments
     *
     * @return PaymentEntityInterface[]
     */
    public function getPayments() : array
    {
        return $this->payments;
    }
}
