<?php

declare(strict_types=1);
/**
 * This file is part of Leonsw.
 *
 * @link     https://leonsw.com
 * @document https://docs.leonsw.com
 * @contact  leonsw.com@gmail.com
 * @license  https://leonsw.com/LICENSE
 */
namespace Demoplayer\Permission;

use Hyperf\Cache\CacheManager;
use Hyperf\Utils\Collection;
use Demoplayer\Permission\Contract\Permission;
use Demoplayer\Permission\Contract\Role;
//use Illuminate\Contracts\Auth\Access\Authorizable;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

class PermissionRegistrar
{
    /** @var \DateInterval|int */
    public static $cacheExpirationTime;

    /** @var string */
    public static $cacheKey;

    /** @var string */
    public static $cacheModelKey;

    /** @var string */
    public static $enableCache;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var CacheInterface */
    protected $cache;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var string */
    protected $permissionClass;

    /** @var string */
    protected $roleClass;

    /** @var Collection */
    protected $permissions;

    /**
     * PermissionRegistrar constructor.
     */
    public function __construct(ContainerInterface $container, CacheManager $cacheManager)
    {
        $this->container = $container;
        $this->permissionClass = config('permission.models.permission');
        $this->roleClass = config('permission.models.role');

        $this->cacheManager = $cacheManager;
        $this->initializeCache();
    }

    /**
     * Flush the cache.
     */
    public function forgetCachedPermissions()
    {
        $this->permissions = null;

        return $this->cache->delete(self::$cacheKey);
    }

    /**
     * Clear class permissions.
     * This is only intended to be called by the PermissionServiceProvider on boot,
     * so that long-running instances like Swoole don't keep old data in memory.
     */
    public function clearClassPermissions()
    {
        $this->permissions = null;
    }

    /**
     * Get the permissions based on the passed params.
     */
    public function getPermissions(array $params = []): Collection
    {
        if ($this->permissions === null && self::$enableCache) {
            if ($this->cache->has(self::$cacheKey)) {
                $this->permissions = $this->cache->get(self::$cacheKey);
            } else {
                $this->permissions = $this->getPermissionClass()
                    ->with('roles')
                    ->get();
                $this->cache->set(self::$cacheKey, $this->permissions, self::$cacheExpirationTime);
            }
        } else {
            $this->permissions = $this->getPermissionClass()
                ->with('roles')
                ->get();
        }
        $permissions = clone $this->permissions;

        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }

        return $permissions;
    }

    /**
     * Get an instance of the permission class.
     */
    public function getPermissionClass(): Permission
    {
        return $this->container->get($this->permissionClass);
    }

    public function setPermissionClass($permissionClass)
    {
        $this->permissionClass = $permissionClass;

        return $this;
    }

    /**
     * Get an instance of the role class.
     */
    public function getRoleClass(): Role
    {
        return $this->container->get($this->roleClass);
    }

    /**
     * Get the instance of the Cache Store.
     * @TODO
     */
    public function getCacheStore()
    {
        return $this->cache->getStore();
    }

    protected function initializeCache()
    {
        self::$cacheExpirationTime = config('permission.cache.expiration_time', config('permission.cache_expiration_time'));

        self::$cacheKey = config('permission.cache.key');
        self::$cacheModelKey = config('permission.cache.model_key');
        self::$enableCache = config('permission.enable_cache', false);

        // the 'default' fallback here is from the permission.php config file, where 'default' means to use config(cache.default)
        $cacheDriver = config('permission.cache.store', 'default');
        $this->cache = $this->cacheManager->getDriver($cacheDriver);
    }
}
