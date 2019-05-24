<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\TaxesCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\TaxEntityInterface;

class TaxesCollectionEntity implements TaxesCollectionEntityInterface
{
    /**
     * @var TaxEntityInterface[]
     */
    protected $taxes = [];

    /**
     * Adds a tax to collection
     *
     * @param  TaxEntityInterface  $tax
     *
     * @return void
     */
    public function addTax(TaxEntityInterface $tax)
    {
        $this->taxes[] = $tax;
    }

    /**
     * Gets all taxes
     *
     * @return TaxEntityInterface[]
     */
    public function getTaxes() : array
    {
        return $this->taxes;
    }
}
