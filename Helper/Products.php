<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\ProductsEndpointInterface;
use Vivapets\Moloni\Helper\ProductCategories;
use Vivapets\Moloni\Helper\MeasurementUnits;
use Vivapets\Moloni\Helper\Taxes;
use Vivapets\Moloni\Helper\Tax\Calculation;

use Vivapets\Moloni\Api\CredentialsInterface;
use Vivapets\Moloni\Model\Entities\TaxesCollectionEntity;
use Vivapets\Moloni\Model\Entities\TaxEntity;

use Magento\Catalog\Model\Product;

class Products
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\ProductsEndpointInterface
     */
    protected $productsApi;

    /**
     * @var \Vivapets\Moloni\Helper\ProductCategories
     */
    protected $productCategoriesService;

    /**
     * @var \Vivapets\Moloni\Helper\MeasurementUnits
     */
    protected $measurementUnitsService;

    /**
    * @var \Vivapets\Moloni\Helper\Taxes
    */
    protected $taxesService;

    /**
     * @var \Vivapets\Moloni\Helper\Tax\Calculation
     */
    protected $taxCalculationService;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\ProductsEndpointInterface  $productsApi
     * @param  \Vivapets\Moloni\Helper\ProductCategories  $productCategoriesService
     * @param  \Vivapets\Moloni\Helper\MeasurementUnits  $measurementUnitsService
     * @param  \Vivapets\Moloni\Helper\Taxes  $taxesService
     * @param  \Vivapets\Moloni\Helper\Tax\Calculation  $taxCalculationService
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        ProductsEndpointInterface $productsApi,
        ProductCategories $productCategoriesService,
        MeasurementUnits $measurementUnitsService,
        Taxes $taxesService,
        Calculation $taxCalculationService
    ) {
        $this->cache = $cache;
        $this->productsApi = $productsApi;
        $this->productCategoriesService = $productCategoriesService;
        $this->measurementUnitsService = $measurementUnitsService;
        $this->taxesService = $taxesService;
        $this->taxCalculationService = $taxCalculationService;
    }

    /**
     * Gets product from Moloni API, if it doesn't exist, adds it
     *
     * @param  \Magento\Catalog\Model\Product  $product
     *
     * @return int
     */
    public function getProduct(Product $product)
    {
        return $this->collectProduct($product->getSku()) ?: $this->insertProduct($product);
    }

    /**
     * Inserts a category to moloni
     *
     * @param  \Magento\Catalog\Model\Product  $product
     *
     * @return int
     */
    public function insertProduct(Product $product)
    {
        $optionalData = [];
        $taxes = new TaxesCollectionEntity();

        $taxRate = $this->taxCalculationService->getProductTaxRate($product);

        if($taxRate > 0) {
            $taxes->addTax(new TaxEntity(
                $this->taxes->getTax($taxRate),
                $taxRate
            ));
        } else {
            $optionalData['exemption_reason'] = Taxes::DEFAULT_EXEMPTION_REASON;
        }

        $response = $this->productsApi->insert(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, // company_id
            $this->productCategoriesService->getCategory('E-commerce'), // category_id
            $product->getName(), // name
            (string)$product->getSku() ?? $product->getId(), // reference
            (float)$product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(), // price
            $this->measurementUnitsService->getMeasurementUnit(), // unit_id
            false, // has_stock
            0.00, // stock
            $taxes, // taxes
            1, // type
            $optionalData // array of optional data
        );

        if(isset($response['product_id'])) {
            $this->cache->save($response['product_id'], "Moloni_Products_{$product->getSku()}");
        }

        return $response['product_id'] ?: null;
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array|null
     */
    private function collectProduct($sku)
    {
        return $this->cache->remember("Moloni_Products_{$sku}", function() use ($sku) {
            $products = $this->productsApi->getByReference(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, $sku);

            return isset($products[0]) ? $products[0]['product_id'] : null;
        });
    }
}
