<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Entities;

use Vivapets\Moloni\Api\Entities\DepartureAddressEntityInterface;
use Vivapets\Moloni\Model\Entities\AbstractEntity;

class DepartureAddressEntity extends AbstractEntity implements DepartureAddressEntityInterface
{
    /**
     * @var string
     */
    const XML_PATH_ORIGIN_STREET_LINE_ONE = 'shipping/origin/street_line1';

    /**
     * @var string
     */
    const XML_PATH_ORIGIN_STREET_LINE_TWO = 'shipping/origin/street_line2';

    /**
     * @param  string  $delivery_departure_address
     * @param  string  $delivery_departure_city
     * @param  string  $delivery_departure_zip_code
     * @param  int  $delivery_departure_countr
     *
     * @return void
     */
    public function __construct(
        ?string $delivery_departure_address,
        ?string $delivery_departure_city,
        ?string $delivery_departure_zip_code,
        ?int $delivery_departure_country
    ) {
        return parent::__construct([
            'delivery_departure_address' => $delivery_departure_address ?? '',
            'delivery_departure_city' => $delivery_departure_city ?? '',
            'delivery_departure_zip_code' => $delivery_departure_zip_code ?? '',
            'delivery_departure_country' => $delivery_departure_country ?? '',
        ]);
    }
}
