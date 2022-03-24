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
namespace Demoplayer\Permission\Contract;

use Hyperf\Database\Model\Relations\BelongsToMany;

interface Permission
{
    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany;

    /**
     * Find a permission by its name.
     *
     * @param null|string $guardName
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a permission by its id.
     *
     * @param null|string $guardName
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Find or Create a permission by its name and guard name.
     *
     * @param null|string $guardName
     */
    public static function findOrCreate(string $name, $guardName): self;
}
