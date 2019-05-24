<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\ProductCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\ProductEntityInterface;

class ProductCollectionEntity implements ProductCollectionEntityInterface
{
    /**
     * @var ProductEntityInterface[]
     */
    protected $products = [];

    /**
     * Adds a product to collection
     *
     * @param  ProductEntityInterface  $product
     *
     * @return void
     */
    public function addProduct(ProductEntityInterface $product)
    {
        $this->products[] = $product;
    }

    /**
     * Gets all products
     *
     * @return ProductEntityInterface[]
     */
    public function getProducts() : array
    {
        return $this->products;
    }
}
