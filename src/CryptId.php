<?php

declare(strict_types=1);

namespace Tx\CryptId;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CryptId
{
        public static function encode(string $uuid): string
        {
                return rtrim(strtr(base64_encode(Crypt::encryptString($uuid)), '+/', '-_'), '=');
        }

        public static function decode(string $hash): string
        {
                $base64 = str_pad(strtr($hash, '-_', '+/'), strlen($hash) % 4 === 0 ? strlen($hash) : strlen($hash) + 4 - strlen($hash) % 4, '=', STR_PAD_RIGHT);
                return Crypt::decryptString(base64_decode($base64));
        }
}
