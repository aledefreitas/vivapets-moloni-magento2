<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model;

use Vivapets\Moloni\Api\CredentialsInterface;
use Vivapets\Moloni\Model\Client;
use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Exceptions\ApiResponseException;
use Vivapets\Moloni\Exceptions\ApiAuthenticationException;

class Session implements CredentialsInterface
{
    /**
     * @var \Vivapets\Moloni\Model\Client
     */
    private $client;

    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param  \Vivapets\Moloni\Model\Client  $client
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     *
     * @return void
     */
    public function __construct(
        Client $client,
        CacheHelper $cache
    ) {
        $this->client = $client;
        $this->cache = $cache;
    }

    /**
     * Authenticates to moloni, generating an access token
     *
     * @throws \Vivapets\Moloni\Exceptions\ApiAuthenticationException
     * @return string
     */
    public function authenticate()
    {
        try {
            $this->accessToken = $this->cache->remember('Moloni_Session_Token', function() {
                $response = $this->client->post('grant', [], [
                    'grant_type' => 'password',
                    'client_id' => self::MOLONI_CREDENTIALS_CLIENTID,
                    'client_secret' => self::MOLONI_CREDENTIALS_SECRET,
                    'username' => self::MOLONI_CREDENTIALS_EMAIL,
                    'password' => self::MOLONI_CREDENTIALS_PASSWORD,
                ]);

                if(!isset($response['access_token'])) {
                    throw new ApiAuthenticationException(__('Access Token was not retrieved from Moloni'));
                }

                return $response['access_token'];
            }, 1500 /* 25 minutes lifetime */);

            return $this->accessToken;
        } catch(ApiResponseException $e) {
            throw new ApiAuthenticationException(__('Error authenticating to Moloni! %1', $e->getMessage()));
        }
    }
}
