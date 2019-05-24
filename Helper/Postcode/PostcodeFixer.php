<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper\Postcode;

class PostcodeFixer
{
    /**
     * Filters zip code in case country id is Portugal
     * Required by moloni
     *
     * @param  null|string  $zip_code
     * @param  null|string  $country_id
     *
     * @return string
     */
    public function filterZipCode(?string $zip_code = null, ?string $country_id = 'PT')
    {
        if(!isset($zip_code)) {
            return '';
        }

        $country_id = $country_id ?? 'PT';

        if($country_id == 'PT') {
            $zip_code = preg_replace('/([^0-9\-]+)/', '', $zip_code);

            if(!preg_match('/^([\d]{4})\-([\d]{3})$/', $zip_code)) {
                if(preg_match('/^([\d]{4})([\d]{3})$/', $zip_code)) {
                    return preg_replace('/^([\d]{4})([\d]{3})$/', "$1-$2", $zip_code);
                }

                return '0000-000';
            }

            return $zip_code;
        }

        return $zip_code;
    }
}
