# Changelog

All notable changes to `email-tracking` will be documented in this file.

# 2.2.0 - 2023-15-02
- Laravel 10 Support  

# 2.1.0 - 2022-09-25

### What's Changed

- On Mail setup, is not required to call

```php
 public function build(): void
    {
        parent::build();
        
        // Finish the E-mail buildUp. The view was already defined
    }

```
The docs have been updated to reflect this change.

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/2.0.0...2.1.0

## 2.1.0 - 2022-09-25

### What's Changed

- Simplify TrackableMail setup @henryavila

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/2.0.0...2.1.0

## 2.0.0 - 2022-07-19

### What's Changed

- Log email body by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/7

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.2.2...2.0.0

## 1.2.2 - 2022-07-15

### What's Changed

- fixes by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/6

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.2.1...1.2.2

## 1.2.1 - 2022-07-08

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.2.0...1.2.1

## 1.2.0 - 2022-07-08

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.1 to 1.3.3 by @dependabot in https://github.com/apps-inteligentes/email-tracking/pull/4
- Laravel Nova 4 support by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/5

### New Contributors

- @dependabot made their first contribution in https://github.com/apps-inteligentes/email-tracking/pull/4

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.1.0...1.2.0

## 1.1.0 - 2022-05-06

## What's Changed

- php version required 8.0.2 by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/2
- Allow to define the Model Connection by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/3

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.0.2...1.1.0

## v.1.0.2 - 2022-05-04

## What's Changed

- php version required 8.0.2 by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/2

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.0.1...1.0.2

## 1.0.1 - 2022-05-04

## What's Changed

- Code optimization by @henryavila in https://github.com/apps-inteligentes/email-tracking/pull/1

## New Contributors

- @henryavila made their first contribution in https://github.com/apps-inteligentes/email-tracking/pull/1

**Full Changelog**: https://github.com/apps-inteligentes/email-tracking/compare/1.0...1.0.1

## 1.0 - 2022-05-04

Versão Inicial. Laravel 9 + Laravel Nova 3
