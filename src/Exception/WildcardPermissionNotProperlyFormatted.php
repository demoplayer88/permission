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
namespace Demoplayer\Permission\Exception;

use InvalidArgumentException;

class WildcardPermissionNotProperlyFormatted extends InvalidArgumentException
{
    public static function create(string $permission)
    {
        return new static("Wildcard permission `{$permission}` is not properly formatted.");
    }
}
