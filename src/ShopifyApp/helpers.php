<?php

/**
 * URL-safe Base64 encoding.
 *
 * Replaces `+` with `-` and `/` with `_` and trims padding `=`.
 *
 * @param string $data The data to be encoded.
 *
 * @return string
 */
function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * URL-safe Base64 decoding.
 *
 * Replaces `-` with `+` and `_` with `/`.
 *
 * Adds padding `=` if needed.
 *
 * @param string $data The data to be decoded.
 *
 * @return string
 */
function base64url_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}


/**
 * @param \Illuminate\Http\Request $request
 * @param array $additional
 * @return array|string[]
 */
function convert_redirect_params(\Illuminate\Http\Request $request, array $additional = [])
{
    $params = array_merge(
        $request->all(),
        $additional
    );
    return array_map(
        function ($v) {
            return is_bool($v) ? ($v ? 'true' : 'false') : $v;
        },
        $params
    );
}
