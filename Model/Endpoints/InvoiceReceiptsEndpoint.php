<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\InvoiceReceiptsEndpointInterface;
use Vivapets\Moloni\Api\Entities\ProductCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\PaymentCollectionEntityInterface;
use Vivapets\Moloni\Api\Entities\DepartureAddressEntityInterface;
use Vivapets\Moloni\Api\Entities\DestinationAddressEntityInterface;

class InvoiceReceiptsEndpoint extends Endpoint implements InvoiceReceiptsEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'invoiceReceipts/';
    }

    /**
     * Gets all invoices documents from moloni, for a given company_id
     *
     * @param  int  $company_id
     *
     * @return mixed
     */
    public function getAll(int $company_id)
    {
        return $this->send('getAll', [ 'company_id' => $company_id]);
    }

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
    ) {
        $payload = [
            'company_id' => $company_id,
            'date' => $date->format('Y-m-d'),
            'expiration_date' => $expiration_date->format('Y-m-d'),
            'document_set_id' => $document_set_id,
            'customer_id' => $customer_id,
            'our_reference' => $our_reference,
            'your_reference' => $your_reference,
            'products' => $products->getProducts(),
            'payments' => $payments->getPayments(),
        ];

        $payload = array_merge($payload, $shipping_departure->getArrayCopy(), $shipping_destination->getArrayCopy());

        if(isset($exchange_currency_id)) {
            $payload = array_merge($payload, [
                'exchange_currency_id' => $exchange_currency_id,
                'exchange_rate' => $exchange_rate,
            ]);
        }

        return $this->send('insert', array_merge($payload, $optionalData));
    }
}
