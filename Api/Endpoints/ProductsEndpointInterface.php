<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

use Vivapets\Moloni\Api\Entities\TaxesCollectionEntityInterface;

interface ProductsEndpointInterface
{
    /**
     * @var string
     */
    const DEFAULT_AT_PRODUCT_CATEGORY = 'M';

    /**
     * Gets all products by reference
     *
     * @param  int  $company_id
     * @param  string  $reference
     *
     * @return mixed
     */
    public function getByReference(int $company_id, string $reference);

    /**
     * Inserts a product
     *
     * @param  int  $company_id
     * @param  int  $category_id
     * @param  string  $name
     * @param  string  $reference
     * @param  float  $price
     * @param  int  $unit_id
     * @param  bool  $has_stock
     * @param  float  $stock
     * @param  null|TaxesCollectionEntityInterface  $taxes
     * @param  null|int  $type
     * @param  null|array  $optionalData
     *
     * @return mixed
     */
    public function insert(
        int $company_id,
        int $category_id,
        string $name,
        string $reference,
        float $price,
        int $unit_id,
        bool $has_stock,
        float $stock,
        ?TaxesCollectionEntityInterface $taxes,
        ?int $type = 1,
        ?array $optionalData = []
    );
}
