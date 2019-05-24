<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\ProductEntityInterface;
use Vivapets\Moloni\Api\Entities\ProductCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\TaxesCollectionEntityInterface;
use Vivapets\Moloni\Model\Entities\AbstractEntity;

class ProductEntity extends AbstractEntity implements ProductEntityInterface
{
    /**
     * @param  int  $product_id
     * @param  string  $name
     * @param  float  $qty
     * @param  float  $price
     * @param  null|ProductCollectionEntityInterface  $child_products
     * @param  null|array  $optionalData
     *
     * @return void
     */
    public function __construct(
        int $product_id,
        string $name,
        float $qty,
        float $price,
        TaxesCollectionEntity $taxes,
        ?string $exemption_reason = null,
        ?float $discount = 0.00,
        ?ProductCollectionEntityInterface $child_products = null,
        ?array $optionalData = []
    ) {
        $payload = [
            'product_id' => $product_id,
            'name' => $name,
            'qty' => $qty,
            'price' => $price,
            'discount' => $discount ?? 0.00,
            'exemption_reason' => $exemption_reason ?? '',
        ];

        if(isset($child_products) and !empty($child_products->getProducts())) {
            $payload['child_products'] = $child_products->getProducts();
        }

        if(!empty($taxes->getTaxes())) {
            $payload['taxes'] = $taxes->getTaxes();
        }

        return parent::__construct(array_merge($payload, $optionalData));
    }
}
