<?php

declare(strict_types=1);

namespace Tx\CryptId;

use Illuminate\Contracts\Encryption\DecryptException;

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

    public function encode(mixed $value): string
    {
        if (! is_string($value) && ! is_int($value)) {
            throw new DecryptException('Invalid id.');
        }

        $string = (string) $value;

        if (trim($string) === '') {
            throw new DecryptException('Invalid id.');
        }

        if ($this->isEncoded($string)) {
            return $string;
        }

        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);

        $encrypted = openssl_encrypt($string, $this->encrypMethod, $key, 0, $iv);

        if (! is_string($encrypted) || $encrypted === '') {
            throw new DecryptException('Unable to encrypt id.');
        }

        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }

    public function decode(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            throw new DecryptException('Invalid encrypted id.');
        }

        if ($this->isUuid($value)) {
            throw new DecryptException('Raw UUID is not allowed.');
        }

        if ($this->isNumericId($value)) {
            throw new DecryptException('Raw numeric id is not allowed.');
        }

        $base64 = str_pad(
            strtr($value, '-_', '+/'),
            strlen($value) % 4 === 0 ? strlen($value) : strlen($value) + 4 - strlen($value) % 4,
            '=',
            STR_PAD_RIGHT
        );

        $decoded = base64_decode($base64, true);

        if ($decoded === false) {
            throw new DecryptException('Invalid encrypted id.');
        }

        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);

        $decrypted = openssl_decrypt($decoded, $this->encrypMethod, $key, 0, $iv);

        if (! is_string($decrypted) || trim($decrypted) === '') {
            throw new DecryptException('Invalid encrypted id.');
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
        return ! $this->isUuid($value)
            && ! $this->isNumericId($value)
            && preg_match('/^[A-Za-z0-9\-_]+$/', $value) === 1
            && strlen($value) > 20;
    }
}
