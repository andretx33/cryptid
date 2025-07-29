# CryptID — Lightweight Encryption for Laravel

`CryptID` is a simple, URL-safe, and reversible encryption tool designed for Laravel applications. It protects identifiers like UUIDs and integer IDs from exposure in plaintext in URLs, forms, and requests — reducing the risk of enumeration and brute-force discovery of sequential IDs.

---

## 🔐 Why use CryptID?

- ✅ Short encrypted output (base64, URL-safe)
- ✅ AES-256-CBC secure symmetric encryption
- ✅ Full control over keys and IV
- ✅ Reversible (for internal use)
- ✅ No reliance on Laravel's long `Crypt::encryptString` output
- ✅ Protects URLs and form values

---

## ✨ Installation

```bash
composer require andretx33/cryptid
```

---

## 🧪 Usage

```php
use CryptId;

$encrypted = CryptId::encode('1fe8120a-c64c-47db-8acb-02195b3074ed');
$decrypted = CryptId::decode($encrypted);
```

---

## 🔐 Security Notes

This package is optimized for **ID obfuscation** (e.g., UUIDs or numeric IDs in URLs/forms). It is **not meant for storing highly sensitive data** like credentials or credit cards unless you manually implement MAC or HMAC for integrity.

- IV is derived deterministically from a secret
- AES-256-CBC is used with keys hashed from secrets
- Output is base64-encoded and URL-safe
- Integrity (tamper protection) is not built-in — use HTTPS and transport-layer protections

---

## 📄 License

MIT © [TX33](https://github.com/andretx33)
---

## ⚙️ Environment Setup

In your Laravel `.env` file, add:

```dotenv
CRYPTID_SECRET_KEY=your-secret-key-here
CRYPTID_SECRET_IV=your-secret-iv-here
```

These values are used internally to derive the encryption key and IV. Keep them secret!
