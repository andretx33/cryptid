# CryptID – Secure & URL-Friendly ID Obfuscation for Laravel

[![Latest Version](https://img.shields.io/packagist/v/iceberg/cryptid.svg?style=flat-square)](https://packagist.org/packages/iceberg/cryptid)
[![License](https://img.shields.io/packagist/l/iceberg/cryptid.svg?style=flat-square)](LICENSE)

**CryptID** is a simple and secure Laravel package for obfuscating UUIDs or internal IDs using AES encryption with Laravel's native `Crypt` service — producing short, URL-safe, non-sequential strings ideal for protecting routes and internal references.

---

## 🔐 Features

- Encrypts UUIDs or any string safely using Laravel's `Crypt::encryptString`
- Produces **short, URL-safe** hashes (`base64` + `strtr`)
- Supports automatic decryption via helper methods or model bindings
- Fully compatible with Laravel >= 10.x
- Easily pluggable via Service Provider and Facade

---

## 🚀 Installation

```bash
composer require iceberg/cryptid
```

> If using Laravel < 5.5, register the service provider and alias manually in `config/app.php`.

---

## ⚙️ Configuration

No configuration required. The package uses your app's `APP_KEY` and Laravel's default cipher (`AES-256-CBC`) from `config/app.php`.

---

## 📦 Usage

### Basic Example

```php
use CryptId;

$uuid = '1fe8120a-c64c-47db-8acb-02195b3074ed';

$encrypted = CryptId::encode($uuid);
// => e.g., "ZGVjcnlwdGVk..."

$decrypted = CryptId::decode($encrypted);
// => "1fe8120a-c64c-47db-8acb-02195b3074ed"
```

---

### 🔄 Reversible Format

The encrypted value is:
- AES-encrypted using `Crypt::encryptString`
- Base64-encoded
- Made URL-safe (`-` and `_` instead of `+` and `/`, no padding)

---

## 🧠 Route Binding Integration

To automatically use obfuscated IDs in URLs:

### In your model (e.g., `Report`):

```php
use CryptId;

public function getRouteKey()
{
    return CryptId::encode($this->uuid);
}

public function resolveRouteBinding($value, $field = null)
{
    $uuid = CryptId::decode($value);
    return self::where('uuid', $uuid)->firstOrFail();
}
```

Now your URLs will contain safe, obfuscated strings like:

```
/reports/YeTr1lBx8JhKe8g
```

---

## ✅ Security

- Uses Laravel's built-in `Crypt` system with **AES-256-CBC** and **HMAC for integrity**
- IV is randomly generated for each encryption (non-deterministic)
- Reversible only with your app's `APP_KEY`
- No need to manage `IV`, `key`, or cipher manually

---

## 🧪 Testing

To run tests:

```bash
vendor/bin/phpunit
```

---

## 📄 License

MIT © [TX](https://github.com/andretx33/cryptid.git

---

## 📬 Support

For enterprise or commercial support, contact [TX](https://github.com/andretx33/cryptid.git).