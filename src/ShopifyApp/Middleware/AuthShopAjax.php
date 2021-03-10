<?php

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use OhMyBrew\ShopifyApp\Exceptions\MissingShopDomainException;
use OhMyBrew\ShopifyApp\Exceptions\SignatureVerificationException;

/**
 * Class AuthShopAjax
 * @package OhMyBrew\ShopifyApp\Middleware
 * @author Alexey Sinkevich
 */
class AuthShopAjax extends AuthShop
{
    /**
     * @var bool
     */
    protected $validateScopes = false;

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (MissingShopDomainException $exception) {
            return \Response::make('', 401);
        } catch (SignatureVerificationException $exception) {
            return \Response::make('', 401);
        }
    }
}