<?php

namespace DAMA\DoctrineTestBundle\Doctrine\Cache;

use Doctrine\Common\Cache\CacheProvider;

class StaticArrayCache extends CacheProvider
{
    /**
     * @var array
     */
    private static $data = [];

    /**
     * @return false|mixed
     */
    protected function doFetch($id)
    {
        return $this->doContains($id) ? self::$data[$id] : false;
    }

    protected function doContains($id): bool
    {
        // isset() is required for performance optimizations, to avoid unnecessary function calls to array_key_exists.
        return isset(self::$data[$id]) || array_key_exists($id, self::$data);
    }

    protected function doSave($id, $data, $lifeTime = 0): bool
    {
        self::$data[$id] = $data;

        return true;
    }

    protected function doDelete($id): bool
    {
        unset(self::$data[$id]);

        return true;
    }

    protected function doFlush(): bool
    {
        self::$data = [];

        return true;
    }

    protected function doGetStats(): ?array
    {
        return null;
    }
}
