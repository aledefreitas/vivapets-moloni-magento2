<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Endpoints;

use Vivapets\Moloni\Api\Entities\ProductCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\PaymentCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\DepartureAddressEntityInterface;
use Vivapets\Moloni\Api\Entities\DestinationAddressEntityInterface;

interface InvoiceReceiptsEndpointInterface
{
    /**
     * Gets all invoices documents from moloni, for a given company_id
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id);

    /**
     * Inserts a new invoice receipt
     *
     * @param  int  $company_id
     * @param  \DateTime  $date
     * @param  \DateTime  $expiration_date
     * @param  int  $document_set_id
     * @param  int  $customer_id
     * @param  string  $our_reference  (Order ID in Magento)
     * @param  string  $your_reference  (Order Increment ID in Magento)
     * @param  ProductCollectionEntityInterface  $products
     * @param  PaymentCollectionEntityInterface  $payments
     * @param  DepartureAddressEntityInterface  $shipping_departure
     * @param  DestinationAddressEntityInterface  $shipping_destination
     * @param  null|int  $exchange_currency_id
     * @param  null|float  $exchange_rate
     * @param  null|array  $optionalData
     *
     * @return mixed
     */
    public function insert(
        int $company_id,
        \DateTime $date,
        \DateTime $expiration_date,
        int $document_set_id,
        int $customer_id,
        string $our_reference,
        string $your_reference,
        ProductCollectionEntityInterface $products,
        PaymentCollectionEntityInterface $payments,
        DepartureAddressEntityInterface $shipping_departure,
        DestinationAddressEntityInterface $shipping_destination,
        ?int $exchange_currency_id = null,
        ?float $exchange_rate = null,
        ?array $optionalData = []
    );
}
