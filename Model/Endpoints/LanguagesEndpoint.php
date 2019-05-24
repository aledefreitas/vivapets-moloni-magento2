<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Endpoints;

use Vivapets\Moloni\Model\Endpoint;
use Vivapets\Moloni\Api\Endpoints\LanguagesEndpointInterface;

class LanguagesEndpoint extends Endpoint implements LanguagesEndpointInterface
{
    /**
     * The api endpoint path uri
     *
     * @return string
     */
    protected function endpoint() : string
    {
        return 'languages/';
    }

    /**
     * Gets all languages
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->send('getAll');
    }
}
