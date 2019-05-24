<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\LanguagesEndpointInterface;

class Languages
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\LanguagesEndpointInterface
     */
    protected $languagesApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\LanguagesEndpointInterface  $languagesApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        LanguagesEndpointInterface $languagesApi
    ) {
        $this->cache = $cache;
        $this->languagesApi = $languagesApi;
    }

    /**
     * Gets data from moloni api
     *
     * @param  string  $language_code
     *
     * @return int  Moloni's language id
     */
    public function getLanguage(string $language_code)
    {
        $language_code = explode('_', $language_code);
        $language_code = strtolower($language_code[0]);

        $languages = $this->collectData();

        return $languages[$language_code] ?? $languages['en'];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_Languages', function() {
            $availableLanguages = $this->languagesApi->getAll();

            $languages = [];

            foreach($availableLanguages as $language) {
                $languages[strtolower($language['code'])] = $language['language_id'];
            }

            return $languages;
        });
    }
}
