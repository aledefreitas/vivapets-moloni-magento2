<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper;

use Vivapets\Moloni\Helper\Cache\CacheHelper;
use Vivapets\Moloni\Api\Endpoints\DocumentSetsEndpointInterface;
use Vivapets\Moloni\Api\CredentialsInterface;

class DocumentSets
{
    /**
     * @var \Vivapets\Moloni\Helper\Cache\CacheHelper
     */
    protected $cache;

    /**
     * @var \Vivapets\Moloni\Api\Endpoints\DocumentSetsEndpointInterface
     */
    protected $documentSetsApi;

    /**
     * @param  \Vivapets\Moloni\Helper\Cache\CacheHelper  $cache
     * @param  \Vivapets\Moloni\Api\Endpoints\DocumentSetsEndpointInterface  $documentSetsApi
     *
     * @return void
     */
    public function __construct(
        CacheHelper $cache,
        DocumentSetsEndpointInterface $documentSetsApi
    ) {
        $this->cache = $cache;
        $this->documentSetsApi = $documentSetsApi;
    }

    /**
     * Gets the default document set from moloni
     *
     * @param  string  $set_name
     *
     * @return int  Moloni's documentSet id
     */
    public function getDocumentSet(string $set_name)
    {
        $documentSets = $this->collectData();

        return $documentSets[strtoupper($set_name)] ?? $documentSets['M'];
    }

    /**
     * Collects and caches moloni api data
     *
     * @return array
     */
    private function collectData()
    {
        return $this->cache->remember('Moloni_DocumentSets', function() {
            $availableDocumentSets = $this->documentSetsApi->getAll(CredentialsInterface::MOLONI_CREDENTIALS_COMPANYID);

            $documentSets = [];

            foreach($availableDocumentSets as $documentSet) {
                $documentSets[strtoupper($documentSet['name'])] = $documentSet['document_set_id'];
            }

            return $documentSets;
        });
    }
}
