<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\TaxEntityInterface;
use Vivapets\Moloni\Model\Entities\AbstractEntity;

class TaxEntity extends AbstractEntity implements TaxEntityInterface
{
    /**
     * @param  int  $tax_id
     * @param  float  $value
     * @param  int  $order
     * @param  null|bool  $cumulative
     *
     * @return void
     */
    public function __construct(
        int $tax_id,
        float $value,
        ?int $order = 1,
        ?bool $cumulative = false
    ) {
        return parent::__construct([
            'tax_id' => $tax_id,
            'value' => $value,
            'order' => $order ?? 1,
            'cumulative' => $cumulative ? 1 : 0,
        ]);
    }
}
