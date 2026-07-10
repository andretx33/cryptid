<?php

declare(strict_types=1);

namespace Tx\CryptId;

use Illuminate\Contracts\Encryption\DecryptException;
use InvalidArgumentException;

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
        $string = $this->normalizePlainId($value);

        if ($this->canDecode($string)) {
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
        $string = $this->normalizeEncryptedId($value);

        if ($this->isUuid($string)) {
            throw new DecryptException('Raw UUID is not allowed.');
        }

        if ($this->isNumericId($string)) {
            throw new DecryptException('Raw numeric id is not allowed.');
        }

        if (! $this->hasEncryptedPayloadFormat($string)) {
            throw new DecryptException('Invalid encrypted id.');
        }

        $base64 = str_pad(
            strtr($string, '-_', '+/'),
            strlen($string) % 4 === 0 ? strlen($string) : strlen($string) + 4 - strlen($string) % 4,
            '=',
            STR_PAD_RIGHT
        );

        $cipherText = base64_decode($base64, true);

        if ($cipherText === false) {
            throw new DecryptException('Invalid encrypted id.');
        }

        $key = hash($this->hashCrypt, $this->secretKey);
        $iv = substr(hash($this->hashCrypt, $this->secretIv), 0, 16);
        $decrypted = openssl_decrypt($cipherText, $this->encrypMethod, $key, 0, $iv);

        if (! is_string($decrypted) || trim($decrypted) === '') {
            throw new DecryptException('Invalid encrypted id.');
        }

        if (! $this->isUuid($decrypted) && ! $this->isNumericId($decrypted)) {
            throw new DecryptException('Invalid encrypted id payload.');
        }

        return $decrypted;
    }

    protected function normalizePlainId(mixed $value): string
    {
        if (! is_string($value) && ! is_int($value)) {
            throw new InvalidArgumentException('ID must be a non-empty string or integer.');
        }

        $string = trim((string) $value);

        if ($string === '') {
            throw new InvalidArgumentException('ID must be a non-empty string or integer.');
        }

        return $string;
    }

    protected function normalizeEncryptedId(mixed $value): string
    {
        if (! is_string($value) || trim($value) === '') {
            throw new DecryptException('Invalid encrypted id.');
        }

        return trim($value);
    }

    protected function canDecode(string $value): bool
    {
        try {
            $this->decode($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }

    protected function isUuid(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $value);
    }

    protected function isNumericId(string $value): bool
    {
        return preg_match('/^[1-9][0-9]*$/', $value) === 1;
    }

    protected function hasEncryptedPayloadFormat(string $value): bool
    {
        return strlen($value) >= 16
            && preg_match('/^[A-Za-z0-9_-]+$/', $value) === 1;
    }
}
