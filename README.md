# Drip
PHP library for making API requests against Drip.

**I'm only just getting started here**

[![Build Status](https://travis-ci.org/drewm/drip.svg)](https://travis-ci.org/drewm/drip)

## Install 

Either download and include, or install via Composer:

```
composer require drewm/drip
```

## Webhooks

```php
use DrewM\Drip\Drip;
$data = Drip::receiveWebhook();
```