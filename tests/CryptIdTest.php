<?php

use Tx\CryptId\CryptId;

test('CryptID correctly encrypts and decrypts UUID values', function () {
    $crypt = new CryptId();

    $original = '1fe8120a-c64c-47db-8acb-02195b3074ed';
    $encrypted = $crypt->encode($original);
    $decrypted = $crypt->decode($encrypted);

    expect($decrypted)->toBe($original);
});

test('CryptID decode keeps raw numeric IDs unchanged for fallback compatibility', function () {
    $crypt = new CryptId();

    expect($crypt->decode('123'))->toBe('123');
});

test('CryptID facade class exists for Laravel auto discovery alias', function () {
    expect(class_exists(\Tx\CryptId\Facades\CryptId::class))->toBeTrue();
});
