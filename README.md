# Drip
PHP library for making token-based API requests against Drip.

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

To request a method without an account ID in the URL, (e.g. list accounts) :

```php
$Drip = new Drip('abc123');
$Response = $Drip->getGlobal('accounts');
```

To then subsequently set an account ID:

```php
$Drip->setAccountID('1234567');
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

If you prefer a pub/sub model, you can register listener callables:

```php
use DrewM\Drip\Drip;

Drip::subscribeToWebhook('subscriber.created', function($data){
	// A subscriber was created
});

Drip::subscribeToWebhook('subscriber.subscribed_to_campaign', function($data){
	// A subscriber was added to a campaign
});
```