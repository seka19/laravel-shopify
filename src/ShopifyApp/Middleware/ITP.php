<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Response for ensuring ITP is followed.
 */
class ITP
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
    }
}
