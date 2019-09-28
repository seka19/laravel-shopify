<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

/**
 * Handling ITP for browsers like Safari.
 */
class AuthITP
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param \Closure                 $next    The "next" action to take.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if we have our test cookie
        if (!Cookie::has(Config::get('shopify-app.app_itp_cookie'))) {
            // Cookie not there, redirect...
            return Redirect::route('session.itp');
        }

        // All good
        return $next($request);
    }
}
