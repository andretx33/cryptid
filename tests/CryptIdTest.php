<?php

use Tx\CryptId\CryptId;

test('CryptID correctly encrypts and decrypts values', function () {
    $crypt = new CryptId();

    $original = '1fe8120a-c64c-47db-8acb-02195b3074ed';
    $encrypted = $crypt->encode($original);
    $decrypted = $crypt->decode($encrypted);

    expect($decrypted)->toBe($original);
});