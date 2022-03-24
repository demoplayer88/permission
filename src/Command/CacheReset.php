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
namespace Demoplayer\Permission\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Utils\ApplicationContext;
use Demoplayer\Permission\PermissionRegistrar;

class CacheReset extends HyperfCommand
{
    protected $name = 'permission:cache-reset';

    public function __construct()
    {
        parent::__construct('permission:cache-reset');
        $this->setDescription('Reset the permission cache');
    }

    public function handle()
    {
        if (ApplicationContext::getContainer()->get(PermissionRegistrar::class)->forgetCachedPermissions()) {
            $this->line('Permission cache flushed.');
        } else {
            $this->line('Unable to flush cache.');
        }
    }
}
