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
namespace Demoplayer\Permission\Traits;

use Hyperf\Utils\ApplicationContext;
use Demoplayer\Permission\PermissionRegistrar;

trait RefreshesPermissionCache
{
    public function bootRefreshesPermissionCache()
    {
        $this->onSaved(function () {
            ApplicationContext::getContainer()->get(PermissionRegistrar::class)->forgetCachedPermissions();
        });

        $this->onDeleted(function () {
            ApplicationContext::getContainer()->get(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }
}
