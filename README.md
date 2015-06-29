# Drip
PHP library for making API requests against Drip.

**I'm only just getting started here**

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