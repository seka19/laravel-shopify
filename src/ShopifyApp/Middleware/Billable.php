<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Services;

/**
 * Responsible for ensuring the shop is being billed.
 */
class Billable
{
    /**
     * Checks if a shop has paid for access.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Config::get('shopify-app.billing_enabled') === true) {
            $shop = ShopifyApp::shop();
            if (!$shop->isFreemium() && !$shop->isGrandfathered() && !$shop->plan) {
                // They're not grandfathered in, and there is no charge or charge was declined... redirect to billing
                $params = [null];
                if (Config::get('shopify-app.auth_jwt')) {
                    $params = array_merge(
                        $params,
                        (new Services\JwtService($request))->billingRoutesParams()
                    );
                }
                return Redirect::route('billing', $params);
            }
        }

        // Move on, everything's fine
        return $next($request);
    }
}
