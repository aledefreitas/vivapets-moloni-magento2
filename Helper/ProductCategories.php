<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\ProductCategoriesEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class ProductCategories
{
    /**
     * @var string
     */
    const CACHE_TAG = 'Moloni_ProductCategories';

    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\ProductCategoriesEndpointInterface
     */
    protected $productCategoriesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\ProductCategoriesEndpointInterface  $productCategoriesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        ProductCategoriesEndpointInterface $productCategoriesApi
    ) {
        $this->cache = $cache;
        $this->productCategoriesApi = $productCategoriesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  string  $category_name
     * @param  null|string  $description
     *
     * @return int  Moloni's product_category id
     */
    public function getCategory(string $category_name, ?string $description = '')
    {
        $productCategories = $this->collectData();

        return $productCategories[$category_name] ?? $this->insertCategory($category_name, $description);
    }

    /**
     * Inserts a category to moloni
     *
     * @param  string  $category_name
     * @param  null|string  $description
     *
     * @return int
     */
    public function insertCategory(string $category_name, ?string $description = '')
    {
        $category = $this->productCategoriesApi->insert(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID,
            $category_name,
            null,
            $description
        );

        $this->cache->remove(self::CACHE_TAG);

        return $category['category_id'] ?: null;
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember(self::CACHE_TAG, function() {
            $availableProductCategories = $this->productCategoriesApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $productCategories = [];

            foreach($availableProductCategories as $product_category) {
                $productCategories[$product_category['name']] = $product_category['category_id'];
            }

            return $productCategories;
        });
    }
}
