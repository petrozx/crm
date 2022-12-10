<?php

namespace core\security;

class TokenConfigure
{


    public static function encode($username, $currentTime, $expireTime, $secretKey, $role = null): string
    {
        $plaintext = json_encode([
            'username' => $username,
            'timeStart' => $currentTime,
            'timeEnd' => $expireTime,
            'role' => $role,
        ]);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $secretKey, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $secretKey, true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
    }

    public static function decode($jwt, $secretKey)
    {
        $c = base64_decode($jwt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $secretKey, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $secretKey, true);
        if (hash_equals($hmac, $calcmac))
        {
            return json_decode($plaintext, true);
        } else {
            return false;
        }
    }
}