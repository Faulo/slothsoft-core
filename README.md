# Slothsoft Core

[![Packagist Version](https://img.shields.io/packagist/v/slothsoft/core)](https://packagist.org/packages/slothsoft/core)
[![PHP Version Support](https://img.shields.io/packagist/php-v/slothsoft/core)](https://www.php.net/)
[![Documentation](https://img.shields.io/badge/docs-reference-blue.svg)](https://faulo.github.io/slothsoft-core/)
[![Test Status](https://github.com/Faulo/slothsoft-core/actions/workflows/ci-tests.yml/badge.svg)](https://github.com/Faulo/slothsoft-core/actions/workflows/ci-tests.yml)
[![license badge](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Shared utility package for slothsoft PHP packages.

This is one of the oldest slothsoft packages. It contains actively used infrastructure, older but still supported utility APIs, and historical compatibility code. The package is kept installable for existing consumers, but not every namespace is recommended for new code.

## Compatibility Policy

Semantic versioning is in effect. Public classes are public API, including public constructors, methods, constants, and properties. Public signatures must remain backward-compatible unless an API-breaking change is specifically requested for a major release.

All code in this package must remain syntactically valid on PHP 7.4 and behaviorally compatible with every PHP version covered by CI. Newer PHP runtimes may be used locally, but PHP 8-only syntax is not allowed.

Bug fixes are allowed in every area of the package, including deprecated and historical APIs. Deprecated APIs should not be used for new code, but they are still expected to keep working for existing consumers, including compatibility fixes for newer PHP versions.

Adding dependencies is acceptable when the dependency is justified by the change and remains compatible with this package's supported PHP versions.

## Current / Supported Areas

These parts are suitable for use in new or maintained slothsoft code:

- `Slothsoft\Core\IO\Writable`
  - Writer interfaces for string, file, DOM, stream, and chunk based data.
  - Adapter, decorator, delegate, and merger implementations.
- `Slothsoft\Core\IO\Psr7`
  - PSR-7 stream helpers and stream implementations.
  - Lazy writer streams, generator streams, process streams, chunked streams, and zlib filtered streams.
- `Slothsoft\Core\StreamWrapper`
  - Stream wrapper abstractions for files, resources, and PSR-7 streams.
- `Slothsoft\Core\StreamFilter`
  - PHP stream filter abstractions and implementations for identity, chunked encoding, gzip, and deflate.
- `Slothsoft\Core\XSLT`
  - XSLT input and adapter layer for PHP/libxml, CLI processors, Saxon, and Saxon/C.
- `Slothsoft\Core\IO\Sanitizer`
  - Small sanitizers for strings, arrays, integers, tokens, and filenames.
- `Slothsoft\Core\MimeTypeDictionary`
  - MIME type and extension lookup helpers.
- `Slothsoft\Core\CacheDirectoryStorage`
  - File-system based ephemeral cache storage.

## Supported Legacy Areas

These APIs are older and may be redesigned later, but they are still intended to be usable and supported:

- `Slothsoft\Core\DOMHelper`
  - DOM parsing, XPath namespace registration, serialization, and XSLT helpers.
- `Slothsoft\Core\FileSystem`
  - Large static file-system helper with old media/archive conventions.
- `Slothsoft\Core\ServerEnvironment`
  - Static process-wide directory and host configuration.
- `Slothsoft\Core\Configuration`
  - Mutable configuration fields used by older static APIs.
- `Slothsoft\Core\Calendar`
  - Small date/time and duration helpers.
- `Slothsoft\Core\CLI`
  - Process execution helper.
- `scripts/bootstrap.php`
  - Global helper functions loaded through Composer.
- `Slothsoft\Core\DBMS`
    - mysqli wrapper layer.

## Historical / Deprecated / Out Of Support

These components are included for historical reasons only. Do not use them for new code. Prefer maintained libraries or package-local implementations instead.

- `Slothsoft\Core\Storage`
  - Old cache and remote loading facade built around `XMLHttpRequest`, DBMS fallback, and static configuration.
- `Slothsoft\Core\XMLHttpRequest`
  - Browser-style HTTP client abstraction over cURL.
- `Slothsoft\Core\CloudFlareScraper`
  - Old Cloudflare challenge workaround.
- `Slothsoft\Core\WebCrawler`
  - Small legacy crawler built on `Storage`.
- `Slothsoft\Core\Image` and `Slothsoft\Core\ImageHelper`
  - Old image helpers depending on optional GD/Imagick behavior.
- `Slothsoft\Core\IO\HTTPFile` and `Slothsoft\Core\IO\HTTPStream`
  - Legacy HTTP-backed file/stream helpers.
- `Slothsoft\Core\RCon`
  - Legacy remote console helper.
- `Slothsoft\Core\Game`
  - Dice, name, and prime helpers retained for old consumers.
- `Slothsoft\Core\InterExec`
  - Legacy execution helper.

## Development Notes

Some APIs use global or static process state, especially `ServerEnvironment`, `Configuration`, `Storage`, and file-system/cache helpers. Tests that exercise stateful behavior should isolate side effects with `@runInSeparateProcess`.

The local development environment should provide the Composer development extensions. If an optional extension or platform feature is unavailable, affected tests may be skipped or impossible to run locally. Skipped tests should be treated as intentionally skipped unless the task is specifically about test skipping.

Use `.editorconfig` for coding style. Additional formatter or static-analysis tooling may be added later, but none is required right now.

## Installation

```bash
composer require slothsoft/core
```

## Requirements

See `composer.json` for required PHP extensions and optional development extensions.
