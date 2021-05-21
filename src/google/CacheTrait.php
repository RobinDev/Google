<?php

namespace rOpenDev\Google;

use Exception;
use rOpenDev\Cache\SimpleCacheFile;
use rOpenDev\Cache\SimpleCacheFile as fileCache;

trait CacheTrait
{
    /** @var mixed Contain the cache folder for SERP results * */
    protected string $cacheFolder = '/tmp';

    /** @var int Contain in seconds, the time cache is valid. Default 1 Day (86400). * */
    protected int $cacheExpireTime = 86400;

    protected bool $previousRequestWasFromCache = false;

    public function setCacheExpireTime($seconds): self
    {
        $this->cacheExpireTime = $seconds;

        return $this;
    }

    /**
     * @param string $cache
     */
    public function setCacheFolder(?string $cache): self
    {
        $this->cacheFolder = null === $cache ? '' : $cache;

        return $this;
    }

    /**
     * Delete cache file for a query ($q).
     *
     * @throws \Exception Where self::$cache is not set
     *
     * @return int Number of files deleted
     */
    public function deleteCacheFiles(): int
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
    protected function getCacheManager(): ?SimpleCacheFile
    {
        if (! $this->cacheFolder) {
            return null;
        }

        $cachePrefix = md5($this->q).'_';

        return fileCache::instance($this->cacheFolder, $cachePrefix);
    }

    /**
     * Return a cache key | A vérifier avec chrome.
     */
    protected function getCacheKey($url = null): string
    {
        $url = $url ?: $this->generateGoogleSearchUrl();
        $url = preg_replace('/&(gbv=1|sei=([a-z0-9]+)(&|$))/i', '&', $url);
        $url = trim($url, '&');

        return sha1($this->page.(int) $this->mobile.':'.$url);
    }

    protected function getCache($url): string
    {
        if ($this->cacheFolder) {
            $source = $this->getCacheManager()->get($this->getCacheKey($url), $this->cacheExpireTime);
            $this->previousRequestWasFromCache = true;

            return $source;
        }

        return '';
    }

    public function setCache($url, $source): void
    {
        if ($this->cacheFolder) {
            $this->getCacheManager()->set($this->getCacheKey($url), $source);
            $this->previousRequestWasFromCache = false;
        }
    }
}
