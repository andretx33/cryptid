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
        // If it is already encoded or is invalid for re-encryption, it returns as is.
        if ($this->isEncoded($string) || $this->isUuid($string) || $this->isNumericId($string)) {
            return $string;
        }

        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);
        $output = openssl_encrypt($string, $this->encrypMethod, $key, 0, $iv);

        return rtrim(strtr(base64_encode($output), '+/', '-_'), '=');
    }

    public function decode(string $string): string
    {
        // If it is already a UUID or number, it returns directly
        if ($this->isUuid($string) || $this->isNumericId($string)) {
            return $string;
        }

        // Base64 adjustment
        $base64 = str_pad(
            strtr($string, '-_', '+/'),
            strlen($string) % 4 === 0 ? strlen($string) : strlen($string) + 4 - strlen($string) % 4,
            '=',
            STR_PAD_RIGHT
        );

        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);
        $decrypted = openssl_decrypt(base64_decode($base64), $this->encrypMethod, $key, 0, $iv);

        // If unable to decode, return as is
        if (!is_string($decrypted) || empty($decrypted)) {
            return $string;
        }

        return $decrypted;
    }

    protected function isUuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $value);
    }

    protected function isNumericId(string $value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }

    protected function isEncoded(string $value): bool
    {
        // Heuristic: looks like modified base64, not UUID, not number
        return !str_contains($value, '-') && strlen($value) > 20;
    }
}
