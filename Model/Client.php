<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model;

use Magento\Framework\HTTP\Client\Curl;
use Vivapets\Moloni\Exceptions\ApiResponseException;

class Client
{
    /**
     * Moloni API URI
     *
     * @var string
     */
    const API_URI = 'https://api.moloni.pt/v1/';

    /**
     * @param  \Magento\Framework\HTTP\Client\Curl  $curl
     *
     * @return void
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * Creates the API call request uri
     *
     * @param  string  $endpoint
     * @param  null|array  $params
     *
     * @return mixed
     */
    private function getUri(string $endpoint, array $params = [])
    {
        $uri = self::API_URI . ltrim($endpoint, '/');

        $params = array_merge($params, [
            'json' => 'true',
            'human_errors' => 'true',
        ]);

        if(!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }

        return $uri;
    }

    /**
     * Executes a POST to the API to request a resource
     *
     * @param  string  $endpoint
     * @param  array  $body
     *
     * @throws \Vivapets\Moloni\Exceptions\ApiResponseException
     *
     * @return array|null
     */
    public function post(string $endpoint, ?array $body = [], ?array $params = [])
    {
        $body = empty($body) ? false : json_encode($body);
        $uri = $this->getUri($endpoint, $params);
        $this->curl->post($uri, $body);

        $rawResponse = $this->curl->getBody();

        if(!empty($rawResponse)) {
            $response = json_decode($rawResponse, true);
        }

        if($this->curl->getStatus() !== 200 && $this->curl->getStatus() !== 100) {
            throw new ApiResponseException(__('An HTTP error occurred while trying to request Moloni Endpoint "%1". Status Code: %2 Response: %3', $endpoint, $this->curl->getStatus(), $this->curl->getBody()));
        }

        if(isset($response['valid']) and $response['valid'] == 0) {
            throw new ApiResponseException(__('An error occurred while trying to request Moloni Endpoint "%1". Response: %2 | Request: %3', $endpoint, $response['msg'], $body));
        }

        if(isset($response[0])) {
            if(isset($response[0]['code']) and isset($response[0]['description'])) {
                throw new ApiResponseException(__('An error occurred while trying to request Moloni Endpoint "%1". Response: %2 | Request: %3', $endpoint, $this->curl->getBody(), $body));
            }
        }

        return $response;
    }
}
