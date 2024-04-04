<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Permission;

class CheckPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $methodToAction = [
            'GET' => 'read',
            'POST' => 'write',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
        ];

        $action = $methodToAction[$request->method()] ?? null;

        if (!$action) {
            return response()->json(['message' => 'Action not supported'], 405);
        }

        // Assuming segment(3) correctly targets the part of the URL indicating the page
        $pageName = $request->segment(3); 

        // Attempt to retrieve permissions from cache
        $cachedPermissions = Cache::get('user_permissions_' . $user->id);

        if (!$cachedPermissions) {
            // If permissions are not in cache, retrieve and cache them
            $permissions = Permission::with('page:id,page_name')
                            ->where('role_id', $user->role_id)
                            ->get()
                            ->mapWithKeys(function ($permission) {
                                return [$permission->page->page_name => $permission->toArray()];
                            });
            Cache::put('user_permissions_' . $user->id, $permissions, now()->addHours(24));
        } else {
          
            $permissions = collect($cachedPermissions);
        }
        // dd($action);
        //dd($permissions);
        
        // dd($permissions[$pageName][$action]);
        
        if ($permissions->has($pageName) && $permissions[$pageName][$action]) {

            return $next($request);
        }

        
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
