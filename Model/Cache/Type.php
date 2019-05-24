<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Model\Cache;

use Magento\Framework\Cache\Frontend\Decorator\TagScope;
use Magento\Framework\App\Cache\Type\FrontendPool;

class Type extends TagScope
{
    /**
     * @var string
     */
    const TYPE_IDENTIFIER = 'vivapets_moloni_cache';

    /**
     * @var string
     */
    private static $CACHE_TAG = 'VIVAPETS_MOLONI_CACHE';

    /**
     * @param  \Magento\Framework\App\Cache\Type\FrontendPool  $cacheFrontendPool
     *
     * @return void
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::$CACHE_TAG);
    }

    /**
     * Gets the cache tag
     *
     * @return string
     */
    public function getCacheTag()
    {
        return self::$CACHE_TAG;
    }

    /**
     * Enforce json encoding
     *
     * {@inheritdoc}
     */
    public function save($data, $identifier, array $tags = [], $lifeTime = null)
    {
        return parent::save(json_encode($data), $identifier, $tags, $lifeTime);
    }

    /**
     * Enforce json decoding
     *
     * {@inheritDoc}
     */
    public function load($identifier)
    {
        return json_decode(parent::load($identifier), true);
    }
}
