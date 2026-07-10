<?php

use Tx\CryptId\CryptId;

test('CryptID correctly encrypts and decrypts UUID values', function () {
    $crypt = new CryptId();

    $original = '1fe8120a-c64c-47db-8acb-02195b3074ed';
    $encrypted = $crypt->encode($original);
    $decrypted = $crypt->decode($encrypted);

    expect($encrypted)->not->toBe($original);
    expect($decrypted)->toBe($original);
});

test('CryptID correctly encrypts and decrypts numeric IDs', function () {
    $crypt = new CryptId();

    $original = '123';
    $encrypted = $crypt->encode($original);
    $decrypted = $crypt->decode($encrypted);

    expect($encrypted)->not->toBe($original);
    expect($decrypted)->toBe($original);
});

test('CryptID does not decode raw UUID values', function () {
    $crypt = new CryptId();

    $crypt->decode('a238310f-15c5-4a20-bb91-b31486472f4a');
})->throws(Throwable::class);

test('CryptID does not decode raw numeric string IDs', function () {
    $crypt = new CryptId();

    $crypt->decode('123');
})->throws(Throwable::class);

test('CryptID does not decode raw numeric integer IDs', function () {
    $crypt = new CryptId();

    $crypt->decode(123);
})->throws(Throwable::class);

test('CryptID does not decode empty values', function ($value) {
    $crypt = new CryptId();

    $crypt->decode($value);
})->with(['', '   ', null])->throws(Throwable::class);

test('CryptID does not return malformed values as fallback', function () {
    $crypt = new CryptId();

    $crypt->decode('this-is-not-valid');
})->throws(Throwable::class);

test('CryptID facade class exists for Laravel auto discovery alias', function () {
    expect(class_exists(\Tx\CryptId\Facades\CryptId::class))->toBeTrue();
});
