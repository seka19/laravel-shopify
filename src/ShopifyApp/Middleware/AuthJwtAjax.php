<?php

namespace OhMyBrew\ShopifyApp\Middleware;

/**
 * Class AuthJwtAjax
 * @package OhMyBrew\ShopifyApp\Middleware
 * @author Alexey Sinkevich
 */
class AuthJwtAjax extends AuthShopAjax
{
    /**
     * @var bool
     */
    protected $validateSession = false;
}