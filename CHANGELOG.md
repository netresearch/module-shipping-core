# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.8.0

Magento 2.4.4 compatibility release

### Added

- Support for Magento 2.4.4
- Send shipping label to configured email address, suggested via issue [#45](https://github.com/netresearch/dhl-shipping-m2/issues/45).

### Removed

- Support for PHP 7.1

## 2.7.1

### Fixed

- Return documents download in customer account.

## 2.7.0

### Added

- Carrier-agnostic framework for module configuration validation.
- Persist return shipment labels retrieved from label APIs.
- Display return shipment labels in customer account.
- Send return shipment labels to customer email address.
- Display carrier icons in _Shipping Settings_ configuration groups.

### Changed

- Set selected delivery location as order shipping address.

### Fixed

- Prevent comma as decimal separator in _Packages_ configuration.
- Accept full hours as cut-off time calculation input.

## 2.6.0

### Added

- Add content type option "Commercial Goods" for dutiable shipments.

## 2.5.0

### Added

- Expose services for interactive mass action.

## 2.4.0

### Added

- Carrier-agnostic return shipments framework.

## 2.3.1

### Fixed

- Save gift card product in catalog (work around core bug).

## 2.3.0

### Added

- Add ability to apply customs regulations based on postal codes.

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
