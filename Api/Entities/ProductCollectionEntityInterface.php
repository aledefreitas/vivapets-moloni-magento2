<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Entities;

use Vivapets\Moloni\Api\Entities\ProductEntityInterface;

interface ProductCollectionEntityInterface
{
    /**
     * Adds a product to collection
     *
     * @param  ProductEntityInterface  $product
     */
    public function addProduct(ProductEntityInterface $product);

    /**
     * Gets all products
     *
     * @return ProductEntityInterface[]
     */
    public function getProducts() : array;
}
