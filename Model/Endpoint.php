<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model;

use Vivapets\Moloni\Model\Client;
use Vivapets\Moloni\Model\Session;
use Vivapets\Moloni\Api\EndpointInterface;
use Vivapets\Moloni\Exceptions\ApiResponseException;
use Vivapets\Moloni\Exceptions\ApiAuthenticationException;

abstract class Endpoint implements EndpointInterface
{
    /**
     * @var \Vivapets\Moloni\Model\Client
     */
    protected $client;

    /**
     * @var \Vivapets\Moloni\Model\Session
     */
    protected $session;

    /**
     * @param  \Vivapets\Moloni\Model\Client  $client
     * @param  \Vivapets\Moloni\Model\Session  $session
     *
     * @return void
     */
    public function __construct(
        Client $client,
        Session $session
    ) {
        $this->client = $client;
        $this->session = $session;
    }

    /**
     * The api endpoint path uri
     *
     * @return string
     */
    abstract protected function endpoint() : string;

    /**
     * Sends the request to endpoint
     *
     * @param  string  $method
     * @param  null|array  $body
     * @param  null|array  $params
     *
     * @throws \Vivapets\Moloni\Exceptions\ApiResponseException
     *
     * @return bool|mixed
     */
    protected function send(string $method, ?array $body = [], ?array $params = [])
    {
        $endpointUri = $this->endpoint() . ltrim($method) . '/';

        try {
            $accessToken = $this->session->authenticate();

            $params = array_merge($params, [
                'access_token' => $accessToken,
            ]);

            return $this->client->post($endpointUri, $body, $params);
        } catch(ApiAuthenticationException $e) {
            // @TODO: Implement proper error handling
            echo $e->getMessage();
            return false;
        }
    }
}
