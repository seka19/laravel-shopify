<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use OhMyBrew\ShopifyApp\Services;
use OhMyBrew\ShopifyApp\Exceptions;
use OhMyBrew\ShopifyApp\Services\ShopSession;

/**
 * Class BillingPage
 * @package OhMyBrew\ShopifyApp\Middleware
 * @author Alexey Sinkevich
 */
class BillingPage
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->input('shop');
        if (!$domain) {
            throw new Exceptions\MissingShopDomainException('Unable to get shop domain.');
        }

        $hash = $request->input('hash');
        if ($hash !== Services\BillingPlan::billingRoutesHash($domain)) {
            throw new Exceptions\SignatureVerificationException('Unable to verify hash.');
        }

        if (!$this->validateShop($domain)) {
            throw new Exceptions\HttpException('Failed to authorize shop.');
        }

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

        if (Config::get('shopify-app.auth_jwt')) {
            // If auth_jwt is false, $shopDomain already has to be present is Session due to previously passed auth
            $session->setDomain($domain);
        }

        return true;
    }
}