# BuzzingPixel DataModel Changelog

## 1.3.0

- Added the ability to add arbitrary errors onto attributes

## 1.2.0

### New

- Added DateTimeZone model data type

### Fixed

- Fixed an issue where an integer would not be accepted as a timestamp for setting DateTime data type

## 1.1.0

### New

- Added DateTime model data type

### Fixed

- Fixed an issue where array handlers might throw an error on validation when their properties were empty

## 1.0.1

### Fixed

- Fixed an issue with checking `isset` on result of expression in older versions of PHP

## 1.0.0

Initial release
