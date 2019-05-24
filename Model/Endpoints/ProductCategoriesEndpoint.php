<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\ProductCategoriesEndpointInterface;

class ProductCategoriesEndpoint extends Endpoint implements ProductCategoriesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'productCategories/';
    }

    /**
     * Gets all product categories
     *
     * @param  int  $company_id
     * @param  null|int  $parent_id
     *
     * @return mixed
     */
    public function getAll(int $company_id, ?int $parent_id = 0)
    {
        return $this->send('getAll', [
            'company_id' => $company_id,
            'parent_id' => $parent_id ?? 0,
        ]);
    }

    /**
     * Inserts a product category
     *
     * @param  int  $company_id
     * @param  string  $name
     * @param  null|int  $parent_id
     * @param  null|string  $description
     * @param  null|bool  $pos_enabled
     *
     * @return mixed
     */
    public function insert(
        int $company_id,
        string $name,
        ?int $parent_id = 0,
        ?string $description = '',
        ?bool $pos_enabled = false
    ) {
        return $this->send('insert', [
            'company_id' => $company_id,
            'parent_id' => $parent_id ?? 0,
            'name' => $name,
            'description' => $description ?? '',
            'pos_enabled' => $pos_enabled ? 1 : 0,
        ]);
    }
}
