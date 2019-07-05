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
use Vivapets\Moloni\Logger\Logger;

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
     * @var \Vivapets\Moloni\Logger\Logger
     */
    protected $logger;

    /**
     * @param  \Vivapets\Moloni\Model\Client  $client
     * @param  \Vivapets\Moloni\Model\Session  $session
     * @param  \Vivapets\Moloni\Logger\Logger  $logger
     *
     * @return void
     */
    public function __construct(
        Client $client,
        Session $session,
        Logger $logger
    ) {
        $this->client = $client;
        $this->session = $session;
        $this->logger = $logger;
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
            $this->logger->info(sprintf('[MOLONI AUTH ERROR] %s', $e->getMessage()));
            return false;
        } catch(ApiResponseException $e) {
            $this->logger->info(sprintf('[MOLONI API RESPONSE ERROR] %s', $e->getMessage()));
            return false;
        } catch(\Exception $e) {
            $this->logger->info(sprintf('[MOLONI INTEGRATION ERROR] %s', $e->getMessage()));
            return false;
        }
    }
}
