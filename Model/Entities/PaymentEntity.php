<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\PaymentEntityInterface;
use Vivapets\Moloni\Model\Entities\AbstractEntity;

class PaymentEntity extends AbstractEntity implements PaymentEntityInterface
{
    /**
     * @param  int  $payment_method_id
     * @param  \DateTime  $date
     * @param  float  $value
     * @param  null|string  $notes
     *
     * @return void
     */
    public function __construct(
        int $payment_method_id,
        \DateTime $date,
        float $value,
        ?string $notes = null
    ) {
        return parent::__construct([
            'payment_method_id' => $payment_method_id,
            'date' => $date->format('Y-m-d'),
            'value' => $value,
            'notes' => $notes ?? '',
        ]);
    }
}
