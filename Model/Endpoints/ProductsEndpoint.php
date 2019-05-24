<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\ProductsEndpointInterface;

use Vivapets\Moloni\Api\Entities\TaxesCollectionEntityInterface;

class ProductsEndpoint extends Endpoint implements ProductsEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'products/';
    }

    /**
     * Gets all products by reference
     *
     * @param  int  $company_id
     * @param  string  $reference
     *
     * @return mixed
     */
    public function getByReference(int $company_id, string $reference)
    {
        return $this->send('getByReference', [
            'company_id' => $company_id,
            'reference' => $reference,
        ]);
    }

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
    ) {
        $payload = [
            'company_id' => $company_id,
            'category_id' => $category_id,
            'name' => $name,
            'reference' => $reference,
            'price' => $price,
            'unit_id' => $unit_id,
            'has_stock' => $has_stock,
            'stock' => $stock,
            'taxes' => $taxes->getTaxes(),
            'type' => $type ?? 1,
        ];

        return $this->send('insert', array_merge($payload, $optionalData));
    }
}
