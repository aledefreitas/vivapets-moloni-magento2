<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Vivapets\Moloni\Api\Endpoints\InvoiceReceiptsEndpointInterface;
use Vivapets\Moloni\Helper\PaymentMethods;
use Vivapets\Moloni\Helper\Taxes;
use Vivapets\Moloni\Helper\Customers;
use Vivapets\Moloni\Helper\Products;
use Vivapets\Moloni\Helper\Currencies;
use Vivapets\Moloni\Helper\MaturityDates;
use Vivapets\Moloni\Helper\DocumentSets;
use Vivapets\Moloni\Helper\Countries;
use Vivapets\Moloni\Helper\Tax\Calculation;
use Vivapets\Moloni\Helper\Postcode\PostcodeFixer;

use Vivapets\Moloni\Api\CredentialsInterface;
use Vivapets\Moloni\Model\Entities\ProductCollectionEntity;
use Vivapets\Moloni\Model\Entities\ProductEntity;
use Vivapets\Moloni\Model\Entities\TaxesCollectionEntity;
use Vivapets\Moloni\Model\Entities\TaxEntity;
use Vivapets\Moloni\Model\Entities\PaymentCollectionEntity;
use Vivapets\Moloni\Model\Entities\PaymentEntity;
use Vivapets\Moloni\Model\Entities\DepartureAddressEntity;
use Vivapets\Moloni\Model\Entities\DestinationAddressEntity;

use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Store\Model\Store;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Sales\Model\Order\Item;

class InvoiceReceipts
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\InvoiceReceiptsEndpointInterface
     */
    protected $invoiceReceiptsApi;

    /**
     * @var \Vivapets\Moloni\Helper\PaymentMethods
     */
    protected $paymentMethodsService;

    /**
     * @var \Vivapets\Moloni\Helper\Taxes
     */
    protected $taxesService;

    /**
     * @var \Vivapets\Moloni\Helper\Customers
     */
    protected $customersService;

    /**
     * @var \Vivapets\Moloni\Helper\Products
     */
    protected $productsService;

    /**
     * @var \Vivapets\Moloni\Helper\ShippingProduct
     */
    protected $shippingProductService;

    /**
     * @var \Vivapets\Moloni\Helper\Currencies
     */
    protected $currenciesService;

    /**
     * @var \Vivapets\Moloni\Helper\MaturityDates
     */
    protected $maturityDatesService;

    /**
     * @var \Vivapets\Moloni\Helper\DocumentSets
     */
    protected $documentSetsService;

    /**
     * @var \Vivapets\Moloni\Helper\Countries
     */
    protected $countriesService;

    /**
     * @var \Vivapets\Moloni\Helper\Tax\Calculation
     */
    protected $taxCalculationService;

    /**
     * @var \Vivapets\Moloni\Helper\Postcode\PostcodeFixer
     */
    protected $postcodeFixer;

    /**
     * @param  \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param  \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param  \Vivapets\Moloni\Api\Endpoints\InvoiceReceiptsEndpointInterface  $invoiceReceiptsApi
     * @param  \Vivapets\Moloni\Helper\PaymentMethods  $paymentMethodsService
     * @param  \Vivapets\Moloni\Helper\Taxes  $taxesService
     * @param  \Vivapets\Moloni\Helper\Customers  $customersService
     * @param  \Vivapets\Moloni\Helper\Products  $productsService
     * @param  \Vivapets\Moloni\Helper\ShippingProduct  $shippingProductService
     * @param  \Vivapets\Moloni\Helper\Currencies  $currenciesService
     * @param  \Vivapets\Moloni\Helper\MaturityDates  $maturityDatesService
     * @param  \Vivapets\Moloni\Helper\DocumentSets  $documentSetsService
     * @param  \Vivapets\Moloni\Helper\Countries  $countriesService
     * @param  \Vivapets\Moloni\Helper\Tax\Calculation  $taxCalculationService
     * @param  \Vivapets\Moloni\Helper\Postcode\PostcodeFixer  $postcodeFixer
     *
     * @return void
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        InvoiceReceiptsEndpointInterface $invoiceReceiptsApi,
        PaymentMethods $paymentMethodsService,
        Taxes $taxesService,
        Customers $customersService,
        Products $productsService,
        ShippingProduct $shippingProductService,
        Currencies $currenciesService,
        MaturityDates $maturityDatesService,
        DocumentSets $documentSetsService,
        Countries $countriesService,
        Calculation $taxCalculationService,
        PostcodeFixer $postcodeFixer
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->invoiceReceiptsApi = $invoiceReceiptsApi;
        $this->paymentMethodsService = $paymentMethodsService;
        $this->taxesService = $taxesService;
        $this->customersService = $customersService;
        $this->productsService = $productsService;
        $this->shippingProductService = $shippingProductService;
        $this->currenciesService = $currenciesService;
        $this->maturityDatesService = $maturityDatesService;
        $this->documentSetsService = $documentSetsService;
        $this->countriesService = $countriesService;
        $this->taxCalculationService = $taxCalculationService;
        $this->postcodeFixer = $postcodeFixer;
    }

    /**
     * Creates an invoice receipt and sends it to Moloni
     *
     * @param  \Magento\Sales\Model\Order  $order
     *
     * @return mixed
     */
    public function createInvoiceReceipt(Order $order)
    {
        $store = $this->storeManager->getStore($order->getStoreId());
        $store_currency = $store->getCurrentCurrency()->getCode();
        $base_currency = $store->getBaseCurrency()->getCode();

        $products = $this->buildProductCollection($order);
        $payments = $this->buildPayment($order->getPayment()->getMethodInstance(), $order->getBaseGrandTotal());
        $shipping_departure = $this->buildDepartureAddress($store);

        $shippingAddress = $order->getShippingAddress();
        $shipping_destination = new DestinationAddressEntity(
            $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
            implode(', ', $shippingAddress->getStreet()), // delivery_destination_address
            trim($shippingAddress->getCity() . ' ' . $shippingAddress->getRegion()), // delivery_destination_city
            $this->postcodeFixer->filterZipCode($shippingAddress->getPostcode(), $shippingAddress->getCountryId()), // delivery_destination_zip_code
            $this->countriesService->getCountry($shippingAddress->getCountryId()) // delivery_destination_country
        );

        $currency_id = null;
        $currency_exchange_rate = null;
        $currency_total_note = null;

        if($store_currency !== $base_currency) {
            $currency_id = $this->currenciesService->getCurrency($store_currency);

            if(isset($currency_id)) {
                $currency_exchange_rate = $store->getBaseCurrency()->getRate($store_currency);
            } else {
                $totalCurrentCurrency = $store->getCurrentCurrency()->format($order->getGrandTotal(), [], false);
                $currency_total_note = "Order total in currency ({$store_currency}): {$totalCurrentCurrency}";
            }
        }

        $document_set_series = $order->getStoreId() == 2 ? 'M' : 'V';

        return $this->invoiceReceiptsApi->insert(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, // company_id
            new \DateTime(), // date
            new \DateTime(), // expiration_date
            $this->documentSetsService->getDocumentSet($document_set_series), // document_set_id
            $this->customersService->getCustomer($order), // customer_id
            $order->getIncrementId(), // our_reference
            $order->getEntityId(), // your_reference
            $products, // products
            $payments, // payments
            $shipping_departure, // [ delivery_departure_address, delivery_departure_city, delivery_departure_zip_code, delivery_departure_country ]
            $shipping_destination, // [ delivery_destination_address, delivery_destination_city, delivery_destination_zip_code, delivery_destination_country ]
            $currency_id, // exchange_currency_id
            $currency_exchange_rate, // exchange_rate
            [
                'status' => 0,
                'notes' => $currency_total_note . '
                EORI PT514753790',
            ]
        );
    }

    /**
     * Builds the product entity object
     *
     * @param  \Magento\Sales\Model\Order\Item  $product
     * @param  \Magento\Sales\Model\Order  $order
     *
     * @return \Vivapets\Moloni\Model\Entities\ProductCollectionEntity
     */
    private function buildProductEntity(Item $product, Order $order)
    {
        $child_products = null;
        $product_name = $product->getName();

        if($product->getProduct()->getTypeId() === Configurable::TYPE_CODE) {
            $usedProducts = $product->getChildrenItems();
            if(count($usedProducts) > 0) {
                $productOptions = $product->getProductOptions();
                $options = [];

                if(isset($productOptions['attributes_info']) and count($productOptions['attributes_info']) > 0) {
                    foreach($productOptions['attributes_info'] as $option) {
                        $options[] = "{$option['label']}: {$option['value']}";
                    }
                }

                if(count($options) > 0) {
                    $product_name .= ' - ' . implode('-', $options);
                }
            }
        }

        $product_id = $this->productsService->getProduct($product->getProduct());
        $product_qty = (float)$product->getQtyOrdered();

        $exemption_reason = null;
        $taxes = new TaxesCollectionEntity();

        $taxRate = $this->taxCalculationService->getProductOrderTaxRate($order, $product->getProduct());

        if($taxRate > 0) {
            $taxes->addTax(new TaxEntity(
                $this->taxesService->getTax($taxRate),
                $taxRate
            ));
        } else {
            $exemption_reason = Taxes::DEFAULT_EXEMPTION_REASON;
        }

        $price = round((($product->getBaseOriginalPrice() * 100) / (100+$taxRate)), 5);
        $discountPercent = 0;

        $priceWithDiscount = (float)$product->getBasePriceInclTax();
        $priceDiscount = (float)$product->getBaseOriginalPrice() - $priceWithDiscount;

        if($priceDiscount > 0) {
            $discountPercent = (round((float)$priceDiscount,2) / round((float)$product->getBaseOriginalPrice(), 2)) * 100;
        }

        return new ProductEntity(
            $product_id,
            $product_name,
            $product_qty,
            $price,
            $taxes,
            $exemption_reason,
            $discountPercent,
            $child_products
        );
    }

    /**
     * Builds the product collection object
     *
     * @param  \Magento\Sales\Model\Order  $order
     *
     * @return \Vivapets\Moloni\Model\Entities\ProductCollectionEntity
     */
    private function buildProductCollection(Order $order)
    {
        $products = new ProductCollectionEntity();

        foreach($order->getAllVisibleItems() as $product) {
            $products->addProduct($this->buildProductEntity($product, $order));
        }

        if(count($products->getProducts()) > 0) {
            $exemption_reason = null;
            $taxes = new TaxesCollectionEntity();
            $taxRate = $this->taxCalculationService->getShippingTaxRate($order);

            if($taxRate > 0) {
                $taxes->addTax(new TaxEntity(
                    $this->taxesService->getTax($taxRate),
                    $taxRate
                ));
            } else {
                $exemption_reason = Taxes::DEFAULT_EXEMPTION_REASON;
            }

            $shipping_cost = round((($order->getBaseShippingInclTax() * 100) / (100 + $taxRate)), 5);

            $products->addProduct(new ProductEntity(
                $this->shippingProductService->getShipping(),
                $order->getShippingDescription(),
                1.00,
                $shipping_cost,
                $taxes,
                $exemption_reason
            ));
        }

        return $products;
    }

    /**
     * Builds the payment object
     *
     * @param  mixed  $paymentMethod
     * @param  mixed  $grandTotal
     *
     * @return \Vivapets\Moloni\Model\Entities\PaymentCollectionEntity
     */
    private function buildPayment($paymentMethod, $grandTotal)
    {
        $payments = new PaymentCollectionEntity();

        $payments->addPayment(new PaymentEntity(
            $this->paymentMethodsService->getPaymentMethod($paymentMethod->getTitle()),
            new \DateTime(),
            $grandTotal
        ));

        return $payments;
    }

    /**
     * Builds shipping departure address
     *
     * @param  \Magento\Store\Model\Store  $store
     *
     * @return \Vivapets\Moloni\Model\Entities\DepartureAddressEntity
     */
    private function buildDepartureAddress(Store $store)
    {
        $country_id = $this->scopeConfig->getValue(
            ShippingConfig::XML_PATH_ORIGIN_COUNTRY_ID,
            ScopeInterface::SCOPE_WEBSITES,
            $store->getWebsite()->getCode()
        );

        $postcode = null;
        $city = null;
        $street_line_one = null;
        $street_line_two = null;

        if($store->getConfig('general/country/default') != 'PT') {
            $postcode = $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_POSTCODE,
                ScopeInterface::SCOPE_WEBSITES,
                $store->getWebsite()->getCode()
            );

            $postcode = $this->postcodeFixer->filterZipCode($postcode, $country_id);

            $city = $this->scopeConfig->getValue(
                ShippingConfig::XML_PATH_ORIGIN_CITY,
                ScopeInterface::SCOPE_WEBSITES,
                $store->getWebsite()->getCode()
            );

            $street_line_one = $this->scopeConfig->getValue(
                DepartureAddressEntity::XML_PATH_ORIGIN_STREET_LINE_ONE,
                ScopeInterface::SCOPE_WEBSITES,
                $store->getWebsite()->getCode()
            );

            $street_line_two = $this->scopeConfig->getValue(
                DepartureAddressEntity::XML_PATH_ORIGIN_STREET_LINE_TWO,
                ScopeInterface::SCOPE_WEBSITES,
                $store->getWebsite()->getCode()
            );
        } else {
            $street_line_one = 'Nas nossas instalações';
        }

        return new DepartureAddressEntity(
            trim($street_line_one . ' ' . $street_line_two), // delivery_departure_address
            $city, // delivery_departure_city
            $postcode, // delivery_departure_zip_code
            $this->countriesService->getCountry($country_id) // delivery_departure_country
        );
    }
}
