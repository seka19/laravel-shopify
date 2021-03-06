<?php

namespace OhMyBrew\ShopifyApp\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use OhMyBrew\ShopifyApp\Exceptions;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;

/**
 * Class JwtService
 * @package OhMyBrew\ShopifyApp\Services
 * @author Alexey Sinkevich
 */
class JwtService
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var bool
     */
    private $tokenValid = false;

    /**
     * @var string
     */
    private $domainFromToken = '';

    /**
     * JwtService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return string|null
     * @throws Exceptions\HttpException
     * @throws Exceptions\MissingShopDomainException
     */
    public function getDomain(): ?string
    {
        if ($this->isTokenSet() && $this->isTokenValid()) {
            $domain = $this->getDomainFromToken();
            if (!$domain) {
                // No domain :(
                throw new Exceptions\MissingShopDomainException('Unable to get shop domain from JWT-token.');
            }
            return $domain;
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function getToken(): ?string
    {
        return $this->request->bearerToken();
    }

    /**
     * @return bool
     */
    private function isTokenSet(): bool
    {
        return !!$this->getToken();
    }

    /**
     * @return bool
     * @throws Exceptions\HttpException
     */
    private function isTokenValid(): bool
    {
        if ($this->tokenValid) {
            return true;
        }

        $token = $this->getToken();

        if (!$token) {
            throw new Exceptions\HttpException('Missing authentication token', 401);
        }

        if (!preg_match('/^eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9\.[A-Za-z0-9\-\_=]+\.[A-Za-z0-9\-\_\=]*$/', $token)) {
            throw new Exceptions\HttpException('Malformed token', 400);
        }

        if (!$this->checkTokenSignature($token)) {
            throw new Exceptions\HttpException('Unable to verify token\'s signature', 400);
        }

        $data = $this->getTokenData($token);

        if (!isset($data->iss) ||
            !isset($data->dest) ||
            !isset($data->aud) ||
            !isset($data->sub) ||
            !isset($data->exp) ||
            !isset($data->nbf) ||
            !isset($data->iat) ||
            !isset($data->jti) ||
            !isset($data->sid)) {
            throw new Exceptions\HttpException('Malformed token', 400);
        }

        /*$now = time();
        $lag = 30;
        if (($now > $data->exp + $lag) || ($now < $data->nbf - $lag) || ($now < $data->iat - $lag)) {
            throw new Exceptions\HttpException('Expired token', 403);
        }*/

        if (!stristr($data->iss, $data->dest)) {
            throw new Exceptions\HttpException('Invalid token', 400);
        }

        if ($data->aud !== Config::get('shopify-app.api_key')) {
            throw new Exceptions\HttpException('Invalid token', 400);
        }

        $this->tokenValid = true;

        return true;
    }

    /**
     * @return string
     */
    private function getDomainFromToken(): string
    {
        if ($this->domainFromToken) {
            return $this->domainFromToken;
        }

        $token = $this->getToken();
        if (!$token) {
            return '';
        }

        $data = $this->getTokenData($token);
        if (!isset($data->dest)) {
            return '';
        }

        $shop = parse_url($data->dest, PHP_URL_HOST);
        if (!$shop) {
            return '';
        }

        $this->domainFromToken = ShopifyApp::sanitizeShopDomain($shop);

        return $this->domainFromToken;
    }

    /**
     * @param string $token
     * @return bool
     */
    private function checkTokenSignature(string $token): bool
    {
        $parts = explode('.', $token);
        $signature = array_pop($parts);
        $check = implode('.', $parts);

        $secret = Config::get('shopify-app.api_secret');
        $hmac = hash_hmac('sha256', $check, $secret, true);
        $encoded = base64url_encode($hmac);

        return $encoded === $signature;
    }

    /**
     * @param string $token
     * @return \stdClass
     */
    private function getTokenData(string $token): \stdClass
    {
        $parts = explode('.', $token);
        $body = base64url_decode($parts[1]);
        return json_decode($body);
    }
}