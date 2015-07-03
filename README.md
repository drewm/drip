# Drip
PHP library for making token-based API requests against Drip.

**I'm only just getting started here**

[![Build Status](https://travis-ci.org/drewm/drip.svg)](https://travis-ci.org/drewm/drip)

## Install 

Either download and include, or install via Composer:

```
composer require drewm/drip
```

## Make a simple request

```php
use DrewM\Drip\Drip;

// Create a new Drip object with 
// 1) Your user's API token
// 2) Your account ID

$Drip = new Drip('abc123', '1234567')

// Make a call

Create a new subscriber:

```php
$result = $Drip->post('subscribers', [
				'email' => 'postmaster@example.com',
			]);
```

List all subscribers:

```php
$result = $Drip->get('subscribers');
```

## Webhooks

You can listen for webhooks in a couple of ways. The most basic is:

```php
use DrewM\Drip\Drip;
$data = Drip::receiveWebhook();
```

If you prefer a pub/sub model, you can register listener callabales:

```php
use DrewM\Drip\Drip;

Drip::subscribeToWebhook('subscriber.created', function($data){
	// A subscriber was created
});

Drip::subscribeToWebhook('subscriber.subscribed_to_campaign', function($data){
	// A subscriber was added to a campaign
});

Drip::receiveWebhook();
```