# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.2.1

### Fixed

- Use prefixed table names if necessary.

## 2.2.0

### Added

- Obtain three-letter country code via dedicated utility.

## 2.1.0

### Added

- Allow registering post-processing rules for the street splitting algorithm.

### Fixed

- Unset additional charges when services get deselected indirectly by an address change or cart item update.

## 2.0.0

### Changed

- Package does now contain all features from `dhl/module-shipping-core`.
- Allow carrier modules to apply custom logic on the street splitting algorithm.

### Fixed

- Prevent broken order detail page for countries with optional postal code, reported by
  [HenKun](https://github.com/HenKun) via issue [#8](https://github.com/netresearch/dhl-module-shipping-core/issues/8).
- Prevent database error when adding an item to cart via the _Recently Ordered_ widget.
- Consider label status when collecting orders for auto-processing.

## 1.0.0

Initial release 
