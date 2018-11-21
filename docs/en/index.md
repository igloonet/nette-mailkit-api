# Quickstart

This extension integrates the mailkit Api into Nette Framework.

## Installation

You can install the extension using this command

```sh
$ composer require igloonet/nette-mailkit-api
```

and enable the extension using your neon config.

```yml
extensions:
	mailkitApi: Igloonet\NetteMailkitApi\DI\MailkitApiExtension
```

## Minimal configuration

```yml
mailkitApi:
	clientId: ''
	clientMd5: ''
```

## Usage

The extension registers the `Igloonet\MailkitApi\MailkitApi` as a service. Simply inject the service and use it.

```php
class Mailer
{
	/** @var \Igloonet\MailkitApi\MailkitApi */
	private $mailkitApi;

	public function __construct(\Igloonet\MailkitApi\MailkitApi $mailkitApi)
	{
		$this->mailkitApi = $mailkitApi;
	}

	// ...

```
Documentation
------------

Learn more in the [documentation](https://github.com/igloonet/mailkit-api/blob/master/docs/en/index.md).

