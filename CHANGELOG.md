# Changelog

All notable changes to `laravel-dto` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.


## 2.1.1 - 2020-04-06

### Changed
- Dependencies were updated


## 2.1.0 - 2020-12-13

### Added
- Global DTO flags in configuration

### Changed
- Minimum version of DTO package in use


## 2.0.0 - 2020-11-07

### Added
- Support for DTO 2
- Support for Laravel 8
- Support for PHP 8
- Improved DTO debugging

### Changed
- Tag name for publishing the configuration

### Removed
- Support for DTO 1
- Support for PHP 7.1 and 7.2


## 1.0.0 - 2020-04-20

### Added
- Artisan command to generate DTOs for Eloquent models
- Factory methods to instantiate a DTO from common Laravel interfaces
- DTO injection resolution via IoC container
- Carbon values converter
- Injected dependency resolution in listeners
- Support for macros in DTOs
