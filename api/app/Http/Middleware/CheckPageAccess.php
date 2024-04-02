<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

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
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
        ];

        $action = $methodToAction[$request->method()] ?? null;

        if (!$action) {
            return response()->json(['message' => 'Action not supported'], 405); 
        }

        $pageName = $request->segment(3); 
        
        //  dd($pageName);
        // $permissions = $user->roles->flatMap->permissions->where('page_name', $pageName)->first();
        
        // // Check if user has the required permission for the action
        // if ($permissions && $permissions->$action) {
            return $next($request);
        //}

        // If user does not have permission, return a 403 Forbidden response
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
