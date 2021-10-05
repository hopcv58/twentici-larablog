<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        list($controller, $method) = explode('@', $request->route()->getActionName());
        $controller = str_replace(['App\Http\Controllers\Backend\\', 'Controller'], '', $controller);

        $crudPermissionsMap = [
            'crud' => ['create', 'store', 'edit', 'update', 'destroy', 'restore', 'forceDestroy', 'index', 'view']
        ];

        $classesMap = [
            'Blog' => 'post',
            'Category' => 'category',
            'User' => 'user'
        ];

        foreach ($crudPermissionsMap as $permission => $methods) {
            if (in_array($method, $methods) && isset($classesMap[$controller])) {
                $currentUser = $request->user();
                $className = $classesMap[$controller];
                if ($className == 'post' && in_array($method, ['edit', 'update', 'destroy', 'restore', 'forceDestroy'])) {
                    if ((!$currentUser->owns(Post::withTrashed()->findOrFail($request->route('blog')), 'author_id'))
                        && (!$currentUser->hasPermission('update-others-post') || !$currentUser->hasPermission('delete-others-post'))) {
                        abort(Response::HTTP_FORBIDDEN, 'Forbidden access!');
                    }
                } elseif (!$currentUser->hasPermission("{$permission}-{$className}")) {
                    abort(Response::HTTP_FORBIDDEN, 'Forbidden access!');
                }
                break;
            }
        }

        return $next($request);
    }
}
