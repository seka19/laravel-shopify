<?php

namespace OhMyBrew\ShopifyApp\Traits;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Charge;
use OhMyBrew\ShopifyApp\Models\Plan;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Requests\StoreUsageCharge;
use OhMyBrew\ShopifyApp\Services;

/**
 * Responsible for billing a shop for plans and usage charges.
 */
trait BillingControllerTrait
{
    /**
     * Redirects to billing screen for Shopify.
     *
     * @param \OhMyBrew\ShopifyApp\Models\Plan $plan The plan.
     *
     * @return \Illuminate\View\View
     */
    public function index(Plan $plan)
    {
        $shop = ShopifyApp::shop();

        // If the plan is null, get a plan
        if (is_null($plan) || ($plan && !$plan->exists)) {
            $certainPlans = Config::get('shopify-app.billing_plans');
            if (isset($certainPlans[$shop->id])) {
                $plan = Plan::where('id', $certainPlans[$shop->id])->first();
            } else {
                $plan = Plan::where('on_install', true)->first();
            }
        }

        // Get the confirmation URL
        $bp = new Services\BillingPlan($shop, $plan);
        $url = $bp->confirmationUrl();

        // Do a fullpage redirect
        return View::make('shopify-app::billing.fullpage_redirect', compact('url'));
    }

    /**
     * Processes the response from the customer.
     *
     * @param \OhMyBrew\ShopifyApp\Models\Plan $plan The plan.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Plan $plan)
    {
        // Activate the plan and save
        $shop = ShopifyApp::shop();
        $bp = new Services\BillingPlan($shop, $plan);
        $bp->setChargeId(Request::query('charge_id'));
        $bp->activate();
        $save = $bp->save();

        if (Config::get('shopify-app.auth_jwt')) {
            return View::make(
                'shopify-app::billing.result_redirect',
                [
                    'shopDomain' => $shop->shopify_domain,
                    'apiKey'     => Config::get('shopify-app.api_key')
                ]
            );
        } else {
            return Redirect::route('home')->with(
                $save ? 'success' : 'failure',
                'billing'
            );
        }
    }

    /**
     * Allows for setting a usage charge.
     *
     * @param \OhMyBrew\ShopifyApp\Requests\StoreUsageCharge $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function usageCharge(StoreUsageCharge $request)
    {
        // Activate and save the usage charge
        $validated = $request->validated();
        $uc = new Services\UsageCharge(ShopifyApp::shop(), $validated);
        $uc->activate();
        $uc->save();

        // All done, return with success
        return isset($validated['redirect']) ?
            Redirect::to($validated['redirect'])->with('success', 'usage_charge') :
            Redirect::back()->with('success', 'usage_charge');
    }
}
