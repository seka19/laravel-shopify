<?php

declare(strict_types=1);

namespace OhMyBrew\ShopifyApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Class BillableAjax
 * @package OhMyBrew\ShopifyApp\Middleware
 * @author Alexey Sinkevich
 */
class BillableAjax extends Billable
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $result = parent::handle($request, $next);
        if ($result instanceof RedirectResponse && $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return \Response::make('', 401);
        }
        return $result;
    }
}