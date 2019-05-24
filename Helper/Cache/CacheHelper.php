<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Helper\Cache;

use Vivapets\Moloni\Model\Cache\Type;
use \Closure;
use Magento\Framework\Locale\ResolverInterface;

class CacheHelper
{
    /**
     * @var  \Vivapets\Moloni\Model\Cache\Type
     */
    protected $cache;

    /**
     * @param  \Vivapets\Moloni\Model\Cache\Type  $cache
     *
     * @return void
     */
    public function __construct(Type $cache)
    {
        $this->cache = $cache;
    }

    /**
     * `Remember` functionality to magento's cache
     *
     * @param  string  $cache_key
     * @param  \Closure  $callable
     * @param  int  $lifetime  Defaults to 1 hour
     * @param  array  $tags
     *
     * @return bool|mixed
     */
    public function remember(string $cache_key, Closure $callable, int $lifetime = 3600, array $tags = [])
    {
        if($data = $this->cache->load($cache_key)) {
            return $data;
        }

        $data = call_user_func($callable);

        $tags = array_merge($tags, [
            $this->cache->getCacheTag(),
        ]);

        if($data) {
            $this->cache->save($data, $cache_key, $tags, $lifetime);

            return $data;
        }

        return false;
    }

    /**
     * Forward methods to magento's cache
     *
     * @param  string  $method
     * @param  array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        // In case this is a save method, we append the cache tag to the $tags parameter
        if($method == 'save') {
            if(isset($args[2]) and is_array($args[2])) {
                $args[2] = array_merge($args[2], [
                    $this->cache->getCacheTag(),
                ]);
            } else {
                $args[2] = [
                    $this->cache->getCacheTag(),
                ];
            }
        } elseif($method == 'clean') {
            if(isset($args[1]) and is_array($args[1])) {
                $args[1] = array_merge($args[1], [
                    $this->cache->getCacheTag(),
                ]);
            }
        }

        return call_user_func([ $this->cache, $method ], ...$args);
    }
}
