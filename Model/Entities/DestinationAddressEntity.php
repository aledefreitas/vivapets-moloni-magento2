<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\DestinationAddressEntityInterface;
use Vivapets\Moloni\Model\Entities\AbstractEntity;

class DestinationAddressEntity extends AbstractEntity implements DestinationAddressEntityInterface
{
    /**
     * @param  null|string  $delivery_destination_customer_name
     * @param  null|string  $delivery_destination_address
     * @param  null|string  $delivery_destination_city
     * @param  null|string  $delivery_destination_zip_code
     * @param  int  $delivery_destination_countr
     *
     * @return void
     */
    public function __construct(
        ?string $delivery_destination_customer_name,
        ?string $delivery_destination_address,
        ?string $delivery_destination_city,
        ?string $delivery_destination_zip_code,
        ?int $delivery_destination_country
    ) {
        $name = $delivery_destination_customer_name ?? '';
        $address = $delivery_destination_address ?? '';

        return parent::__construct([
            'delivery_destination_address' => trim($name . ', '. $address),
            'delivery_destination_city' => $delivery_destination_city ?? '',
            'delivery_destination_zip_code' => $delivery_destination_zip_code ?? '',
            'delivery_destination_country' => $delivery_destination_country ?? '',
        ]);
    }
}
