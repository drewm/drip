# Drip
PHP library for making token-based API requests against Drip.

**I'm only just getting started here**

[![Build Status](https://travis-ci.org/drewm/drip.svg)](https://travis-ci.org/drewm/drip)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/drewm/drip/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/drewm/drip/?branch=master)

## Install 

Either download and include, or install via Composer:

```
composer require drewm/drip
```

## Make a simple request

Create a new Drip object with 

1. Your user's API token (Settings > My User Settings > API Token)
2. Your numeric account ID (Log into the Drip dashboard and it's the first segment in your URL)

```php
use DrewM\Drip\Drip;
use DrewM\Drip\Dataset;

$Drip = new Drip('abc123', '1234567')
```

Create a new subscriber:

```php
$data = new Dataset('subscribers', [
				'email' => 'postmaster@example.com',
			]);
$Response = $Drip->post('subscribers', $data);
```

List all subscribers:

```php
$Response = $Drip->get('subscribers');
```

## Handling responses

Methods return a Response object

```php
if ($Response->status == 200) {
	// all is ok!
	$subscribers = $Response->subscribers;
} else {
	echo $Response->error;
	echo $Response->message;
}
```

Get the raw response:

```php
$raw = $Response->get();
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
```