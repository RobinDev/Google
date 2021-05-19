<?php

namespace rOpenDev\Google;

use Exception;
use rOpenDev\Cache\SimpleCacheFile as fileCache;

trait CacheTrait
{
    /** @var mixed Contain the cache folder for SERP results * */
    protected $cacheFolder = '/tmp';

    /** @var int Contain in seconds, the time cache is valid. Default 1 Day (86400). * */
    protected $cacheExpireTime = 86400;

    public function setCacheExpireTime($seconds)
    {
        $this->cacheExpireTime = $seconds;

        return $this;
    }

    /**
     * @param string $cache
     */
    public function setCacheFolder(?string $cache)
    {
        $this->cacheFolder = $cache;

        return $this;
    }

    /**
     * Delete cache file for a query ($q).
     *
     * @throws \Exception Where self::$cache is not set
     *
     * @return int Number of files deleted
     */
    public function deleteCacheFiles()
    {
        if (! $this->cacheFolder) {
            throw new Exception('Cache Folder is not defined : you can\'t delete cache files');
        }

        return $this->getCacheManager()->getMaintener()->deleteCacheFilesByPrefix();
    }

    /**
     * Return cache instance.
     *
     * @throws \Exception if the cache (folder, self::$cache via self::setCache) is not set
     *
     * @return \rOpenDev\cache\SimpleCacheFile
     */
    protected function getCacheManager()
    {
        if (! $this->cacheFolder) {
            return null;
        }

        $cachePrefix = md5($this->q).'_';

        return fileCache::instance($this->cacheFolder, $cachePrefix);
    }

    /**
     * Return a cache key | A vérifier avec chrome.
     *
     * @return string
     */
    protected function getCacheKey($url = null)
    {
        return sha1($this->page.(int) $this->mobile.':'.($url ?: $this->generateGoogleSearchUrl()));
    }

    protected function getCache($url)
    {
        if ($this->cacheFolder) {
            $source = $this->getCacheManager()->get($this->getCacheKey($url), $this->cacheExpireTime);
            //$this->previousResultCacheKey = $cacheKey;
            return $source;
        }

        return false;
    }

    public function setCache($url, $source)
    {
        if ($this->cacheFolder) {
            $this->getCacheManager()->set($this->getCacheKey($url), $source);
        }
    }
}
