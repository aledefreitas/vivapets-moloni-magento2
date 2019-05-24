<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

interface ProductCategoriesEndpointInterface
{
    /**
     * Gets all product categories
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id, ?int $parent_id = 0);

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
    );
}
