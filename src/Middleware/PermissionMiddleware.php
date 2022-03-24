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
namespace Demoplayer\Permission\Middleware;

use Hyperf\HttpServer\Router\Dispatched;
use Demoplayer\Http\ForbiddenException;
use Demoplayer\Permission\Model\Permission;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    protected $allow = [
        '/account/signin',
        '/account/refresh-token',
        '/account/logout',
        '/account/menu:get',
        '/account/info:get',
        '/account/permission:get',
        '/account/password:put',
        '/account/profile:put',
    ];

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatcher = $request->getAttribute(Dispatched::class);
        $route = $dispatcher->handler->route;

        $path = strtolower($route . ':' . $request->getMethod());

        $user = $request->getAttribute('user');

        if ($user) {
            if (in_array($path, $this->allow) || $this->isSystem((int) $user->id)) {
                return $handler->handle($request);
            }
        }

//        if ($user && $user->roles && in_array(1, $user->roles->pluck('id')->toArray(), true)) {
//            return $handler->handle($request);
//        }

        // $permission->first() = $model->first(); 会获取第一条

        $permission = Permission::findByName($path, $user->guardName());

        if ($user && $permission && $user->checkPermissionTo($permission)) {
            return $handler->handle($request);
        }
        throw new ForbiddenException('没有 ' . $path . ' 接口权限');
    }

    public function isSystem(int $id)
    {
        if ($id === 0) {
            return true;
        }
        return false;
    }
}
