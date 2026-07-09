# Changelog

## [v1.1.2] - 2026-07-09

### Changed
- Confirmed Laravel 10/11 compatibility via `illuminate/support: ^10 || ^11`.

### Fixed
- Added the missing `Tx\CryptId\Facades\CryptId` class referenced by Laravel package auto-discovery.
- Added strict declarations and return types to the service provider.


## [v1.1.0] - 2025-07-28

### Added
- decode() now supports UUIDs, integers, and already-decoded values without throwing errors
- encode() now detects and skips encoding for UUIDs or previously encoded values
- Compatibility for ID fallback patterns (UUID, integer, etc.)

### Changed
- decode() will now return original input if decryption fails instead of throwing a TypeError

## [v1.0.0] - 2025-07-28

### Added
- Initial release of CryptID
- AES-256-CBC encryption for UUIDs and IDs
- Facade and ServiceProvider for Laravel
- URL-safe base64 encoding
- README with usage and security guidance
- Unit test for encryption/decryption
