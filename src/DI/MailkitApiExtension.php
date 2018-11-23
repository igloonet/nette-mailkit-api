<?php
declare(strict_types=1);

namespace Igloonet\NetteMailkitApi\DI;

use Igloonet\MailkitApi\Exceptions\InvalidConfigException;
use Igloonet\MailkitApi\MailkitApi;
use Igloonet\MailkitApi\Managers\MessagesManager;
use Igloonet\MailkitApi\Managers\UsersManager;
use Igloonet\MailkitApi\Managers\MailingListsManager;
use Igloonet\MailkitApi\Managers\WebHooksManager;
use Igloonet\MailkitApi\RPC\Client;
use Nette;

class MailkitApiExtension extends Nette\DI\CompilerExtension
{
	private const ENABLED_LANGUAGES = ['cs', 'en', 'de', 'ru', 'pl', 'sk', 'hu'];

	/** @var array */
	private $defaults = [
		'clientId' => null,
		'clientMd5' =>null,
		'defaultLanguage' => 'cs'
	];

	public function loadConfiguration(): void
	{
		$this->validateConfig($this->defaults);

		if (trim($this->config['clientId'] ?? '') === '') {
			throw new InvalidConfigException('clientId is not configured properly');
		}

		if (trim($this->config['clientMd5'] ?? '') === '') {
			throw new InvalidConfigException('clientMd5 is not configured properly');
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('rpc.client'))
			->setClass(Client::class)
			->setArguments([
				'clientId' => $this->config['clientId'],
				'clientMd5' => $this->config['clientMd5']
			]);

		$managers = [
			'mailingLists' => MailingListsManager::class,
			'users' => UsersManager::class,
			'messages' => MessagesManager::class
		];

		$mailkitApiArgs = [];

		foreach ($managers as $managerName => $managerClass) {
			$builder->addDefinition($this->prefix('managers.'.$managerName))
				->setClass($managerClass)
				->setArguments([
					'client' => '@'.$this->prefix('rpc.client'),
					'enabledLanguages' => self::ENABLED_LANGUAGES,
					'defaultLanguage' => $this->config['defaultLanguage']
				]);

			$mailkitApiArgs[$managerName.'Manager'] = '@'.$this->prefix('managers.'.$managerName);
		}

		$builder->addDefinition($this->prefix('managers.webHooks'))
			->setClass(WebHooksManager::class);

		$mailkitApiArgs['webHooksManager'] = '@'.$this->prefix('managers.webHooks');

		$builder->addDefinition($this->prefix('api'))
			->setClass(MailkitApi::class)
			->setArguments($mailkitApiArgs);

		return;
	}

	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('mailkitApi', new MailkitApiExtension());
		};
	}
}
