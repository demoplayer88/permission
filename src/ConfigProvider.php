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

use Leonsw\Permission\Command\CacheReset;

class ConfigProvider
{
    public function __invoke(): array
    {
        $configSourcePath = __DIR__ . '/../config/permission.php';
        $configDestinationPath = BASE_PATH . '/config/autoload/permission.php';
        return [
            'commands' => [
                CacheReset::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for permission.',
                    'source' => $configSourcePath,
                    'destination' => $configDestinationPath,
                ],
                [
                    'id' => 'database',
                    'description' => 'The database for permission.',
                    'source' => __DIR__ . '/../database/migrations/2014_10_14_000000_create_permission_tables.php',
                    'destination' => BASE_PATH . '/database/migrations/2014_10_14_000000_create_permission_tables.php',
                ],
            ],
            'permission' => file_exists($configDestinationPath) ? [] : require $configSourcePath,
        ];
    }
}
