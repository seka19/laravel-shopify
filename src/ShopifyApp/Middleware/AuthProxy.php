<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Services\ShopSession;

/**
 * Responsible for ensuring a proper app proxy request.
 */
class AuthProxy
{
    /**
     * Handle an incoming request to ensure it is valid.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Grab the query parameters we need, remove signature since its not part of the signature calculation
        $query = $request->query->all();
        $signature = $query['signature'];
        unset($query['signature']);

        // Build a local signature
        $signatureLocal = ShopifyApp::createHmac(['data' => $query, 'buildQuery' => true]);
        if ($signature !== $signatureLocal || !isset($query['shop'])) {
            // Issue with HMAC or missing shop header
            return Response::make('Invalid proxy signature.', 401);
        }

        if (!$this->validateShop($request->get('shop'))) {
            return Response::make('Failed to authorize shop.', 401);
        }

        // All good, process proxy request
        return $next($request);
    }

    /**
     * @param string $domain
     * @return bool
     */
    protected function validateShop(string $domain): bool
    {
        $shopModel = Config::get('shopify-app.shop_model');
        $shop = $shopModel::withTrashed()
            ->where(['shopify_domain' => $domain])
            ->first();

        if ($shop === null || $shop->trashed()) {
            return false;
        }

        $session = new ShopSession();
        $session->setShop($shop);
        $session->setDomain($domain);

        return true;
    }
}
