<?php

namespace OhMyBrew\ShopifyApp\Exceptions;

/**
 * Exception for use in requests that need http responses.
 */
class HttpException extends BaseException
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request The incoming request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
            ], $this->getCode());
        }

        return response($this->getMessage(), $this->getCode());
    }
}
