<?php

declare(strict_types=1);

namespace Tx\CryptId;

class CryptId
{
    public string $hashCrypt = 'sha256';
    public string $secretKey = '';
    public string $secretIv = '';
    public string $encrypMethod = 'AES-256-CBC';

    public function __construct()
    {
        $this->secretKey = config('app.crypt_key', env('CRYPTID_SECRET_KEY', 'default_key'));
        $this->secretIv = config('app.crypt_iv', env('CRYPTID_SECRET_IV', 'default_iv'));
    }

    public function encode(string $string): string
    {
        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);
        $output = openssl_encrypt($string, $this->encrypMethod, $key, 0, $iv);
        return rtrim(strtr(base64_encode($output), '+/', '-_'), '=');
    }

    public function decode(string $string): string
    {
        $base64 = str_pad(strtr($string, '-_', '+/'), strlen($string) % 4 === 0 ? strlen($string) : strlen($string) + 4 - strlen($string) % 4, '=', STR_PAD_RIGHT);
        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);
        return openssl_decrypt(base64_decode($base64), $this->encrypMethod, $key, 0, $iv);
    }
}