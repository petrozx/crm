<?php

namespace core\security;

class TokenConfigure
{
    public static function encode(): string
    {
        $plaintext = json_encode([
            'username' => $_SESSION['AUTH']['NAME'],
            'timeStart' => time(),
            'timeEnd' => time()+ getenv('DURATION'),
            'role' => $_SESSION['AUTH']['ROLE'],
        ]);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, getenv('SECRET_KEY'), OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, getenv('SECRET_KEY'), true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
    }

    public static function decode($jwt)
    {
        $c = base64_decode($jwt);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, getenv('SECRET_KEY'), OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, getenv('SECRET_KEY'), true);
        if (hash_equals($hmac, $calcmac))
        {
            return json_decode($plaintext, true);
        } else {
            return false;
        }
    }
}