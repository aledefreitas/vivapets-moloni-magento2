<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Vivapets\Moloni\Api\Endpoints\CustomersEndpointInterface;
use Vivapets\Moloni\Helper\Countries;
use Vivapets\Moloni\Helper\Languages;
use Vivapets\Moloni\Helper\MaturityDates;
use Vivapets\Moloni\Helper\Postcode\PostcodeFixer;

use Vivapets\Moloni\Api\CredentialsInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Customers
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\CustomersEndpointInterface
     */
    protected $customersApi;

    /**
     * @var \Vivapets\Moloni\Helper\Countries
     */
    protected $countriesService;

    /**
     * @var \Vivapets\Moloni\Helper\Languages
     */
    protected $languagesService;

    /**
     * @var \Vivapets\Moloni\Helper\MaturityDates
     */
    protected $maturityDatesService;

    /**
     * @var \Vivapets\Moloni\Helper\Postcode\PostcodeFixer
     */
    protected $postcodeFixer;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param  \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param  \Vivapets\Moloni\Api\Endpoints\CustomersEndpointInterface  $customersApi
     * @param  \Vivapets\Moloni\Helper\Countries  $countriesService
     * @param  \Vivapets\Moloni\Helper\Languages  $languagesService
     * @param  \Vivapets\Moloni\Helper\MaturityDates  $maturityDatesService
     * @param  \Vivapets\Moloni\Helper\Postcode\PostcodeFixer  $postcodeFixer
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CustomersEndpointInterface $customersApi,
        Countries $countriesService,
        Languages $languagesService,
        MaturityDates $maturityDatesService,
        PostcodeFixer $postcodeFixer
    ) {
        $this->cache = $cache;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->customersApi = $customersApi;
        $this->countriesService = $countriesService;
        $this->languagesService = $languagesService;
        $this->maturityDatesService = $maturityDatesService;
        $this->postcodeFixer = $postcodeFixer;
    }

    /**
     * Gets customer from Moloni API, if it doesn't exist, adds it
     *
     * @param  \Magento\Sales\Api\Data\OrderInterface  $order
     *
     * @return int
     */
    public function getCustomer(OrderInterface $order)
    {
        $billingAddress = $order->getBillingAddress();
        $customer_number = $order->getCustomerId() ?? $this->generateGuestCustomerId($billingAddress);

        $customer_id = $this->collectCustomer($customer_number);

        return $customer_id > 0
            ? $this->updateCustomer($customer_id, $customer_number, $billingAddress, $order->getStoreId())
            : $this->insertCustomer($customer_number, $billingAddress, $order->getStoreId());
    }

    /**
     * Generates a guest customer id
     *
     * @param  \Magento\Sales\Api\Data\OrderAddressInterface  $billingAddress
     *
     * @return string
     */
    private function generateGuestCustomerId(OrderAddressInterface $billingAddress)
    {
        return (string)($this->storeManager->getStore()->getId() . str_pad($billingAddress->getEntityId(), 7, '0', STR_PAD_LEFT));
    }

    /**
     * Inserts a customer to moloni
     *
     * @param  string  $customer_number
     * @param  \Magento\Sales\Api\Data\OrderAddressInterface  $billingAddress
     * @param  null|int  $store_id
     *
     * @return int
     */
    public function insertCustomer(
        string $customer_number,
        OrderAddressInterface $billingAddress,
        ?int $store_id = 0
    ) {
        $store_id = $store_id ?? 0;
        $locale = $this->getStoreLocale($this->storeManager->getStore($store_id));

        $customer = $this->customersApi->insert(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, // company_id
            $billingAddress->getVatId() ?? CustomersEndpointInterface::DEFAULT_VAT_ID, // vat
            $customer_number, // number
            trim($billingAddress->getFirstname() . ' ' . $billingAddress->getLastname()), // name
            $this->languagesService->getLanguage($locale), // language_id
            implode(', ', $billingAddress->getStreet()), // address
            $this->postcodeFixer->filterZipCode($billingAddress->getPostcode(), $billingAddress->getCountryId()), // zip_code
            $billingAddress->getCity(), // city
            $this->countriesService->getCountry($billingAddress->getCountryId()), // country_id
            $this->maturityDatesService->getMaturityDate(), // maturity_date_id
            null, // payment_method_id
            [
                'salesman_id' => 0,
                'payment_day' => 0,
                'discount' => 0,
                'credit_limit' => 0,
                'delivery_method_id' => 0,
            ]
        );

        if(isset($customer['customer_id'])) {
            $this->cache->save($customer['customer_id'], "Moloni_Customers_{$customer_number}");
        }

        return $customer['customer_id'] ?: null;
    }

    /**
     * Updates a customer in moloni
     *
     * @param  int  $customer_id
     * @param  string  $customer_number
     * @param  \Magento\Sales\Api\Data\OrderAddressInterface  $billingAddress
     *
     * @return int
     */
    public function updateCustomer(
        int $customer_id,
        string $customer_number,
        OrderAddressInterface $billingAddress,
        ?int $store_id = 0
    ) {
        $store_id = $store_id ?? 0;
        $locale = $this->getStoreLocale($this->storeManager->getStore($store_id));

        $customer = $this->customersApi->update(
            CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, // company_id
            $customer_id, // customer_id
            $billingAddress->getVatId() ?? CustomersEndpointInterface::DEFAULT_VAT_ID, // vat
            $customer_number, // number
            trim($billingAddress->getFirstname() . ' ' . $billingAddress->getLastname()),
            $this->languagesService->getLanguage($locale), // language_id
            implode(', ', $billingAddress->getStreet()), // address
            $this->postcodeFixer->filterZipCode($billingAddress->getPostcode(), $billingAddress->getCountryId()), // zip_code
            $billingAddress->getCity(), // city
            $this->countriesService->getCountry($billingAddress->getCountryId()), // country_id
            $this->maturityDatesService->getMaturityDate() // maturity_date_id
        );

        if(isset($customer['customer_id'])) {
            $this->cache->save($customer['customer_id'], "Moloni_Customers_{$customer_number}");
        }

        return $customer['customer_id'] ?: null;
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array|null
     */
    private function collectCustomer(int $customer_number)
    {
        return $this->cache->remember("Moloni_Customers_{$customer_number}", function() use ($customer_number) {
            $customers = $this->customersApi->getByNumber(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID, (string)$customer_number);
            return isset($customers[0]) ? $customers[0]['customer_id'] : null;
        });
    }

    /**
     * Gets the store locale configuration
     *
     * @param  \Magento\Store\Model\Store  $store
     *
     * @return string
     */
    private function getStoreLocale(Store $store)
    {
        return $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $store->getCode()) ?? 'en_US';
    }
}
