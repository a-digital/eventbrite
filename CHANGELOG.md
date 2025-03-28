# Eventbrite Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.3 - 2025-03-28
### Changed
- General housekeeping with a code refactor to clean up plugin references, return types, a few conditionals, etc
### Fixed
- Issue [#20](https://github.com/a-digital/eventbrite/issues/20) Type error when an array returns null - Credit to [RobMacKay](https://github.com/RobMacKay) for pull request [#21](https://github.com/a-digital/eventbrite/pull/21)

## 2.0.2 - 2023-09-06
### Changed
- Environment variables added to settings

## 2.0.1 - 2022-09-20
### Changed
- Errors are now suppressed in production environments

## 2.0.0 - 2022-08-11
### Changed
- Update plugin for Craft 4

## 1.0.6 - 2022-02-18
### Fixed
- Amend check for other event IDs as it wasn't being iterated over in more recent versions of Craft

## 1.0.5 - 2020-10-30
### Added
- Add ability to filter by whether events are public or not

### Fixed
- Issue with PSR-4 autoload of widget file

### Changed
- Updated README

## 1.0.4 - 2020-03-12
### Added
- Add functionality to return full HTML description

### Fixed
- Remove hardcoded reference to client name

## 1.0.3 - 2020-01-30
### Fixed
- Refactor event service logic, improve handling when no events by organiser

## 1.0.2 - 2020-01-27
### Fixed
- Merged pull request to correct composer directive

## 1.0.1 - 2019-11-12
### Fixed
- Update asset bundle namespace to head off issue with composer

## 1.0.0 - 2019-11-12
### Added
- Initial release
