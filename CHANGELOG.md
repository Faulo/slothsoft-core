# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Updated dependencies and Composer metadata while retaining PHP 7.4 as the dependency-resolution platform.

## [1.14.27] - 2026-08-31

### Fixed

- Added bootstrap support for the `fpm-fcgi` SAPI and made SAPI-specific bootstrap files optional.

## [1.14.26] - 2026-07-24

### Changed

- Cleaned up Composer release archives and development-environment configuration.

## [1.14.25] - 2026-07-12

### Fixed

- Prevented mysqli reporting changes in PHP 8.1 and later from escaping DBMS operations.

## [1.14.24] - 2026-07-05

### Changed

- Restored the DBMS API to supported status and strengthened type documentation across the package.

### Removed

- Retired the unused `DatabaseException` class.

## [1.14.23] - 2026-07-04

### Changed

- Finalized extensibility-safe classes, added return types, and consolidated file-cache behavior.

## [1.14.22] - 2026-07-04

### Changed

- Declared the runtime extensions used by the package.
- Improved type safety and finalized classes that were not designed for inheritance.

### Deprecated

- Marked historical APIs as deprecated while retaining them for compatibility.

### Fixed

- Fixed stream positions, DOM writing and normalization, and initialization edge cases.

## [1.14.21] - 2026-05-12

### Fixed

- Initialized nullable `XMLHttpRequest` response fields before a request completes.

## [1.14.20] - 2026-05-12

### Changed

- Declared the DOM, libxml, cURL, and mbstring extensions as runtime requirements.

### Fixed

- Cleaned up `XMLHttpRequest` handling and cURL resource shutdown.

## [1.14.19] - 2026-03-13

### Fixed

- Replaced obsolete backtick execution in `HTTPFile` with `shell_exec`.

## [1.14.18] - 2026-03-12

### Changed

- Marked optional parameters explicitly nullable for forward compatibility.

## [1.14.17] - 2026-01-20

### Added

- Added an optional cache-refresh callback to file-cache writers.

## [1.14.16] - 2026-01-20

### Added

- Added MIME types for common font formats.

## [1.14.15] - 2026-01-11

### Changed

- Tightened stream-wrapper registration and implementation behavior.

## [1.14.14] - 2026-01-07

### Fixed

- Fixed Eclipse project configuration.

## [1.14.13] - 2026-01-07

### Fixed

- Fixed `PersistentGeneratorStream::getSize()` after indexed reads.

## [1.14.12] - 2026-01-06

### Changed

- Replaced `LazyOpenStream` with `LazyFileWriterStream`.

## [1.14.11] - 2026-01-06

### Changed

- Made DOM writers produce documents without retaining a cached document instance.

## [1.14.10] - 2026-01-06

### Added

- Added `LazyStringWriterStream`.

### Fixed

- Fixed stream `getContents()` edge cases.

## [1.14.9] - 2026-01-01

### Fixed

- Fixed size reporting in `OneTimeGeneratorStream`.

## [1.14.8] - 2026-01-01

### Added

- Added one-time and persistent generator stream implementations.

## [1.14.7] - 2025-12-31

### Fixed

- Fixed stream test compatibility and test-class naming collisions.

## [1.14.6] - 2025-12-31

### Fixed

- Fixed caching in chunk, string, and DOM writer decorators.
- Reset delegated chunk generators between reads.

## [1.14.5] - 2025-12-30

### Fixed

- Fixed generator stream behavior across empty, partial, repeated, and boundary reads.

## [1.14.4] - 2025-12-28

### Added

- Added XSL and XSD document-type detection to `DOMHelper`.

## [1.14.3] - 2025-12-01

### Added

- Added `ImageHelper::createSpriteSheetFromImages()`.

## [1.14.2] - 2025-11-18

### Fixed

- Applied writable permissions to files and directories created by `FileSystem::copy()`.

## [1.14.1] - 2025-11-18

### Fixed

- Made `FileSystem::copy()` report source path resolution failures as exceptions.

## [1.14.0] - 2025-10-21

### Removed

- Moved the `DOMNodeEqualTo` test constraint to `slothsoft/farah-testing`.

## [1.13.19] - 2025-10-21

### Added

- Added the XML Schema Instance namespace constant `DOMHelper::NS_XSI`.

## [1.13.18] - 2025-10-15

### Fixed

- Fixed nested DOM difference reporting in `DOMNodeEqualTo`.

## [1.13.17] - 2025-10-15

### Fixed

- Fixed DOM difference reporting in `DOMNodeEqualTo`.

## [1.13.16] - 2025-10-11

### Fixed

- Added the missing bootstrap for PHP's `cli-server` SAPI.

## [1.13.15] - 2025-10-10

### Fixed

- Normalized line-ending comparisons in `DOMNodeEqualTo`.

## [1.13.14] - 2025-10-08

### Added

- Added the `DOMNodeEqualTo` PHPUnit constraint with processing-instruction and text-coalescing support.

## [1.13.13] - 2025-10-06

### Added

- Added `FileInfoFactory::createDirectoryFromPath()`.

### Fixed

- Fixed directory creation in `ImageHelper`.

## [1.13.12] - 2025-10-04

### Fixed

- Fixed removal of directory symlinks in `FileSystem::removeDir()`.

## [1.13.11] - 2025-10-04

### Fixed

- Prevented `FileSystem::removeDir()` from deleting the contents targeted by symlinks.

## [1.13.10] - 2025-09-28

### Changed

- Strengthened typing across writer adapters, decorators, delegates, and mergers.

## [1.13.9] - 2025-09-28

### Added

- Added `MimeTypeDictionary::isText()` and `MimeTypeDictionary::isHtml()`.

## [1.13.8] - 2025-09-17

### Changed

- Renamed `IEphemeralStorage` to `EphemeralStorageInterface` and normalized source formatting.

## [1.13.7] - 2025-09-17

### Added

- Added the XInclude namespace constant `DOMHelper::NS_XINCLUDE`.

## [1.13.6] - 2025-09-17

### Fixed

- Fixed stream stat calls for paths without a registered URL scheme.

## [1.13.5] - 2025-09-02

### Changed

- Documented optional image, database, networking, and large-file extensions in Composer metadata.

## [1.13.4] - 2025-09-02

### Changed

- Excluded development-only files from Composer distribution archives.

## [1.13.3] - 2025-08-27

### Changed

- Replaced storage installation with a `CacheDirectoryStorage` fallback.
- Removed the explicit storage installation step from `IEphemeralStorage`.

### Fixed

- Corrected the `php-ds/php-ds` dependency and PHP return-type compatibility attributes.

## [1.13.2] - 2025-08-15

### Added

- Added the `image/webp` MIME type.

### Fixed

- Fixed DBMS exception handling, stream-filter reporting, and storage locking behavior.

## [1.13.1] - 2025-07-04

### Changed

- Moved generated API documentation deployment to GitHub Pages.

## [1.13.0] - 2025-06-25

### Added

- Added `MYSQL_HOST`, `MYSQL_USER`, `MYSQL_PASSWORD`, and `MYSQL_PASSWORD_FILE` configuration support.

## [1.12.1] - 2025-06-23

### Fixed

- Prevented `FileSystem::filenameEncode()` from double-encoding UTF-8 names.

## [1.12.0] - 2025-06-22

### Added

- Added ephemeral cache storage through `IEphemeralStorage`, `CacheDirectoryStorage`, and `StorageConfigurationField`.
- Added `FileSystem::ensureDirectory()`.

### Fixed

- Generated proper file URIs and used change times for cache invalidation.

## [1.11.17] - 2024-10-30

### Fixed

- Fixed `LeanElement` unserialization with typed properties.

## [1.11.16] - 2024-10-30

### Fixed

- Declared the stream-wrapper context property required by PHP.

## [1.11.15] - 2024-10-30

### Fixed

- Corrected the stream type stored by `StreamWrapperRegistrar`.

## [1.11.14] - 2024-10-30

### Changed

- Upgraded `guzzlehttp/psr7` to version 2.7 or later.

### Fixed

- Fixed nullable stream-wrapper factories and initialized the active stream field.

## [1.11.13] - 2024-10-29

### Fixed

- Required a Symfony Process version compatible with supported PHP releases.

## [1.11.12] - 2024-10-29

### Fixed

- Corrected the `ReturnTypeWillChange` attribute on `CascadingDictionary`.

## [1.11.11] - 2024-10-29

### Changed

- Replaced the deprecated `Serializable` interface with modern serialization methods.

## [1.11.10] - 2024-10-29

### Fixed

- Fixed modern serialization of `CloudFlareScraper`.

## [1.11.9] - 2024-10-29

### Fixed

- Fixed modern serialization of `LeanElement`.

## [1.11.8] - 2024-10-29

### Fixed

- Added PHP 8.1-compatible return types and serialization methods to legacy APIs.

## [1.11.7] - 2024-10-05

### Changed

- Raised the minimum supported PHP version to 7.4.

## [1.11.6] - 2024-09-29

### Changed

- Removed obsolete Composer classmap configuration from package metadata.

## [1.11.5] - 2024-09-23

### Changed

- Updated package metadata for the `main` branch and normalized release files.

## [1.11.4] - 2024-04-12

### Added

- Added a default `.farah` configuration file to distribution archives.

## [1.11.3] - 2024-04-12

### Fixed

- Fixed file-writability detection.

## [1.11.2] - 2024-04-01

### Fixed

- Corrected Composer distribution export attributes.

## [1.11.1] - 2024-04-01

### Changed

- Updated Composer branch aliases and package export configuration.

## [1.11.0] - 2023-08-28

### Added

- Added support for PHP 8.2.

## [1.10.0] - 2022-08-13

### Added

- Added recursive file and directory copying through `FileSystem::copy()`.

### Changed

- Added return and parameter types to `FileSystem` helpers.

### Fixed

- Fixed byte-size formatting in `FileSystem::drawBytes()`.

## [1.9.0] - 2022-08-06

### Added

- Added the `server-clean` command for clearing data, log, and cache directories.

### Changed

- Standardized file handling on UTF-8.

## [1.8.2] - 2022-07-15

### Fixed

- Made `FileSystem::commandExists()` portable on Linux and suppressed lookup noise.

## [1.8.1] - 2022-07-13

### Changed

- Moved `ChunkWriterFromProcess` to the writer adapter namespace and removed the duplicate generator delegate.

### Fixed

- Fixed chunk reads from stream writers.

## [1.8.0] - 2022-07-12

### Changed

- Renamed `FileSystem::commandExist()` to `FileSystem::commandExists()`.

## [1.7.0] - 2022-07-11

### Added

- Added `FileSystem::commandExist()` for executable discovery.

## [1.6.0] - 2022-07-10

### Added

- Added `CascadingDictionary` and a generator-backed chunk writer delegate.

### Changed

- Changed the project license from WTFPL to MIT.

## [1.5.4] - 2022-06-29

### Fixed

- Fixed a documentation typo and refreshed dependencies.

## [1.5.3] - 2022-06-25

### Changed

- Adopted the shared `temp_file()` helper for temporary file creation.

## [1.5.2] - 2022-04-04

### Added

- Added `ChunkWriterFromGenerator`.

### Fixed

- Fixed custom subdirectory paths created by `temp_dir()`.

## [1.5.1] - 2022-01-21

### Fixed

- Fixed filename sanitization and switched character conversion from iconv to mbstring.

## [1.5.0] - 2022-01-18

### Added

- Added `FileSystem::removeDir()`, `normalize_slashes()`, and temporary-directory helpers.

## [1.4.1] - 2022-01-08

### Fixed

- Fixed wrong-document DOM exceptions.

## [1.4.0] - 2022-01-05

### Added

- Added `MimeTypeDictionary::isXml()`.

### Changed

- Included the package version in generated cache keys.

## [1.3.1] - 2022-01-04

### Added

- Added support for PHP 8.1.

## [1.3.0] - 2020-12-27

### Added

- Added `ChunkWriterFromProcess` and `TokenSanitizer`.

## [1.2.1] - 2020-12-26

### Changed

- Expanded automated test coverage across public classes, interfaces, and traits.

## [1.2.0] - 2020-12-26

### Added

- Added support for PHP 8.0.

### Changed

- Updated public parameter and return types for newer dependency interfaces.

## [1.1.3] - 2020-12-25

### Fixed

- Fixed the MIME type dictionary resource path.

## [1.1.2] - 2020-12-25

### Changed

- Switched autoloading from PSR-0 to PSR-4 and moved bootstrap files into `scripts`.
- Updated dependency constraints and the PHPUnit test suite.

## [1.1.1] - 2020-12-24

### Added

- Added generated API documentation and its build configuration.

## [1.1.0] - 2020-12-24

### Added

- Added the `CLI` process execution API.

### Changed

- Cleaned up legacy code and refreshed PHP and dependency requirements.

## [1.0.4] - 2020-12-01

### Added

- Added the `application/wasm` MIME type.

### Fixed

- Fixed XSLT parameter handling and DBMS column definitions.

## [1.0.3] - 2019-11-17

### Added

- Added the `image/x-icon` MIME type.

### Fixed

- Made DBMS escaping accept string-convertible values.

## [1.0.2] - 2018-10-22

### Changed

- Made `LeanElement` children iterable and backed them with `Ds\Vector`.

### Fixed

- Prevented partial writer output from being persisted to file caches.

## [1.0.1] - 2018-08-27

### Fixed

- Fixed XSLT parameter names and values passed to `XSLTProcessor`.

## [1.0.0] - 2018-08-10

Initial release.

[unreleased]: https://github.com/Faulo/slothsoft-core/compare/1.14.27...HEAD
[1.14.27]: https://github.com/Faulo/slothsoft-core/compare/1.14.26...1.14.27
[1.14.26]: https://github.com/Faulo/slothsoft-core/compare/1.14.25...1.14.26
[1.14.25]: https://github.com/Faulo/slothsoft-core/compare/1.14.24...1.14.25
[1.14.24]: https://github.com/Faulo/slothsoft-core/compare/1.14.23...1.14.24
[1.14.23]: https://github.com/Faulo/slothsoft-core/compare/1.14.22...1.14.23
[1.14.22]: https://github.com/Faulo/slothsoft-core/compare/1.14.21...1.14.22
[1.14.21]: https://github.com/Faulo/slothsoft-core/compare/1.14.20...1.14.21
[1.14.20]: https://github.com/Faulo/slothsoft-core/compare/1.14.19...1.14.20
[1.14.19]: https://github.com/Faulo/slothsoft-core/compare/1.14.18...1.14.19
[1.14.18]: https://github.com/Faulo/slothsoft-core/compare/1.14.17...1.14.18
[1.14.17]: https://github.com/Faulo/slothsoft-core/compare/1.14.16...1.14.17
[1.14.16]: https://github.com/Faulo/slothsoft-core/compare/1.14.15...1.14.16
[1.14.15]: https://github.com/Faulo/slothsoft-core/compare/1.14.14...1.14.15
[1.14.14]: https://github.com/Faulo/slothsoft-core/compare/1.14.13...1.14.14
[1.14.13]: https://github.com/Faulo/slothsoft-core/compare/1.14.12...1.14.13
[1.14.12]: https://github.com/Faulo/slothsoft-core/compare/1.14.11...1.14.12
[1.14.11]: https://github.com/Faulo/slothsoft-core/compare/1.14.10...1.14.11
[1.14.10]: https://github.com/Faulo/slothsoft-core/compare/1.14.9...1.14.10
[1.14.9]: https://github.com/Faulo/slothsoft-core/compare/1.14.8...1.14.9
[1.14.8]: https://github.com/Faulo/slothsoft-core/compare/1.14.7...1.14.8
[1.14.7]: https://github.com/Faulo/slothsoft-core/compare/1.14.6...1.14.7
[1.14.6]: https://github.com/Faulo/slothsoft-core/compare/1.14.5...1.14.6
[1.14.5]: https://github.com/Faulo/slothsoft-core/compare/1.14.4...1.14.5
[1.14.4]: https://github.com/Faulo/slothsoft-core/compare/1.14.3...1.14.4
[1.14.3]: https://github.com/Faulo/slothsoft-core/compare/1.14.2...1.14.3
[1.14.2]: https://github.com/Faulo/slothsoft-core/compare/1.14.1...1.14.2
[1.14.1]: https://github.com/Faulo/slothsoft-core/compare/1.14.0...1.14.1
[1.14.0]: https://github.com/Faulo/slothsoft-core/compare/1.13.19...1.14.0
[1.13.19]: https://github.com/Faulo/slothsoft-core/compare/1.13.18...1.13.19
[1.13.18]: https://github.com/Faulo/slothsoft-core/compare/1.13.17...1.13.18
[1.13.17]: https://github.com/Faulo/slothsoft-core/compare/1.13.16...1.13.17
[1.13.16]: https://github.com/Faulo/slothsoft-core/compare/1.13.15...1.13.16
[1.13.15]: https://github.com/Faulo/slothsoft-core/compare/1.13.14...1.13.15
[1.13.14]: https://github.com/Faulo/slothsoft-core/compare/1.13.13...1.13.14
[1.13.13]: https://github.com/Faulo/slothsoft-core/compare/1.13.12...1.13.13
[1.13.12]: https://github.com/Faulo/slothsoft-core/compare/1.13.11...1.13.12
[1.13.11]: https://github.com/Faulo/slothsoft-core/compare/1.13.10...1.13.11
[1.13.10]: https://github.com/Faulo/slothsoft-core/compare/1.13.9...1.13.10
[1.13.9]: https://github.com/Faulo/slothsoft-core/compare/1.13.8...1.13.9
[1.13.8]: https://github.com/Faulo/slothsoft-core/compare/1.13.7...1.13.8
[1.13.7]: https://github.com/Faulo/slothsoft-core/compare/1.13.6...1.13.7
[1.13.6]: https://github.com/Faulo/slothsoft-core/compare/1.13.5...1.13.6
[1.13.5]: https://github.com/Faulo/slothsoft-core/compare/1.13.4...1.13.5
[1.13.4]: https://github.com/Faulo/slothsoft-core/compare/1.13.3...1.13.4
[1.13.3]: https://github.com/Faulo/slothsoft-core/compare/1.13.2...1.13.3
[1.13.2]: https://github.com/Faulo/slothsoft-core/compare/1.13.1...1.13.2
[1.13.1]: https://github.com/Faulo/slothsoft-core/compare/1.13.0...1.13.1
[1.13.0]: https://github.com/Faulo/slothsoft-core/compare/1.12.1...1.13.0
[1.12.1]: https://github.com/Faulo/slothsoft-core/compare/1.12.0...1.12.1
[1.12.0]: https://github.com/Faulo/slothsoft-core/compare/1.11.17...1.12.0
[1.11.17]: https://github.com/Faulo/slothsoft-core/compare/1.11.16...1.11.17
[1.11.16]: https://github.com/Faulo/slothsoft-core/compare/1.11.15...1.11.16
[1.11.15]: https://github.com/Faulo/slothsoft-core/compare/1.11.14...1.11.15
[1.11.14]: https://github.com/Faulo/slothsoft-core/compare/1.11.13...1.11.14
[1.11.13]: https://github.com/Faulo/slothsoft-core/compare/1.11.12...1.11.13
[1.11.12]: https://github.com/Faulo/slothsoft-core/compare/1.11.11...1.11.12
[1.11.11]: https://github.com/Faulo/slothsoft-core/compare/1.11.10...1.11.11
[1.11.10]: https://github.com/Faulo/slothsoft-core/compare/1.11.9...1.11.10
[1.11.9]: https://github.com/Faulo/slothsoft-core/compare/1.11.8...1.11.9
[1.11.8]: https://github.com/Faulo/slothsoft-core/compare/1.11.7...1.11.8
[1.11.7]: https://github.com/Faulo/slothsoft-core/compare/1.11.6...1.11.7
[1.11.6]: https://github.com/Faulo/slothsoft-core/compare/1.11.5...1.11.6
[1.11.5]: https://github.com/Faulo/slothsoft-core/compare/1.11.4...1.11.5
[1.11.4]: https://github.com/Faulo/slothsoft-core/compare/1.11.3...1.11.4
[1.11.3]: https://github.com/Faulo/slothsoft-core/compare/1.11.2...1.11.3
[1.11.2]: https://github.com/Faulo/slothsoft-core/compare/1.11.1...1.11.2
[1.11.1]: https://github.com/Faulo/slothsoft-core/compare/1.11.0...1.11.1
[1.11.0]: https://github.com/Faulo/slothsoft-core/compare/1.10.0...1.11.0
[1.10.0]: https://github.com/Faulo/slothsoft-core/compare/1.9.0...1.10.0
[1.9.0]: https://github.com/Faulo/slothsoft-core/compare/1.8.2...1.9.0
[1.8.2]: https://github.com/Faulo/slothsoft-core/compare/1.8.1...1.8.2
[1.8.1]: https://github.com/Faulo/slothsoft-core/compare/1.8.0...1.8.1
[1.8.0]: https://github.com/Faulo/slothsoft-core/compare/1.7.0...1.8.0
[1.7.0]: https://github.com/Faulo/slothsoft-core/compare/1.6.0...1.7.0
[1.6.0]: https://github.com/Faulo/slothsoft-core/compare/1.5.4...1.6.0
[1.5.4]: https://github.com/Faulo/slothsoft-core/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/Faulo/slothsoft-core/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/Faulo/slothsoft-core/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/Faulo/slothsoft-core/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/Faulo/slothsoft-core/compare/1.4.1...1.5.0
[1.4.1]: https://github.com/Faulo/slothsoft-core/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/Faulo/slothsoft-core/compare/1.3.1...1.4.0
[1.3.1]: https://github.com/Faulo/slothsoft-core/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/Faulo/slothsoft-core/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/Faulo/slothsoft-core/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/Faulo/slothsoft-core/compare/1.1.3...1.2.0
[1.1.3]: https://github.com/Faulo/slothsoft-core/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/Faulo/slothsoft-core/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/Faulo/slothsoft-core/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/Faulo/slothsoft-core/compare/1.0.4...1.1.0
[1.0.4]: https://github.com/Faulo/slothsoft-core/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/Faulo/slothsoft-core/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Faulo/slothsoft-core/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Faulo/slothsoft-core/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Faulo/slothsoft-core/releases/tag/1.0.0
