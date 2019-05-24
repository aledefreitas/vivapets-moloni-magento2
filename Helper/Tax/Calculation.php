<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper\Tax;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Magento\Tax\Model\Config as TaxConfig;

use Magento\Catalog\Model\Product;
use Magento\Sales\Model\Order;

class Calculation
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * @param  \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param  \Magento\Tax\Model\Calculation  $taxCalculation
     * @param  \Magento\Tax\Model\Config  $taxConfig
     *
     * @return void
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TaxCalculation $taxCalculation,
        TaxConfig $taxConfig
    ) {
        $this->storeManager = $storeManager;
        $this->taxCalculation = $taxCalculation;
        $this->taxConfig = $taxConfig;
    }

    /**
     * Gets a given product's tax rate percent
     *
     * @param  \Magento\Catalog\Model\Product  $product
     *
     * @return float
     */
    public function getProductTaxRate(Product $product)
    {
        $taxRateRequest = $this->taxCalculation->getRateRequest(null, null, null, $this->storeManager->getStore(0));
        $taxRateRequest->setProductClassId($product->getTaxClassId());

        return $this->taxCalculation->getRate($taxRateRequest);
    }

    /**
     * Gets a given order product's tax rate percent
     *
     * @param  \Magento\Sales\Model\Order  $order
     * @param  \Magento\Catalog\Model\Product  $product
     *
     * @return float
     */
    public function getProductOrderTaxRate(Order $order, Product $product)
    {
        $taxRateRequest = $this->taxCalculation->getRateRequest(
            $order->getShippingAddress(),
            $order->getBillingAddress(),
            null,
            $this->storeManager->getStore($order->getStoreId()),
            $order->getCustomerId()
        );

        $taxRateRequest->setProductClassId($product->getTaxClassId());

        return $this->taxCalculation->getRate($taxRateRequest);
    }

    /**
     * Calculates and returns the shipping tax percentage
     *
     * @param  \Magento\Sales\Model\Order  $order
     *
     * @return float
     */
    public function getShippingTaxRate(Order $order)
    {
        $taxRateRequest = $this->taxCalculation->getRateRequest(
            $order->getShippingAddress(),
            $order->getBillingAddress(),
            null,
            $this->storeManager->getStore($order->getStoreId())
        );

        $taxRateRequest->setProductClassId($this->taxConfig->getShippingTaxClass());

        return $this->taxCalculation->getRate($taxRateRequest);
    }
}
