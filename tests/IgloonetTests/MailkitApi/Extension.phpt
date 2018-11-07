<?php

namespace IgloonetTests\MailkitApi;

use Igloonet\NetteMailkitApi\DI\MailkitApiExtension;
use Nette;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class ExtensionTest extends TestCase
{
	protected function createContainer($configFile = 'default')
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5($configFile)]]);
		MailkitApiExtension::register($config);
		$config->addConfig(__DIR__ . '/config/' . $configFile . '.neon');

		return $config->createContainer();
	}

	public function testFunctional()
	{
		$sl = $this->createContainer();
		Assert::type('Igloonet\MailkitApi\MailkitApi', $sl->getService('mailkitApi.api'));
		Assert::type('Igloonet\MailkitApi\Managers\MailingListsManager', $sl->getService('mailkitApi.managers.mailingLists'));
		Assert::type('Igloonet\MailkitApi\Managers\MessagesManager', $sl->getService('mailkitApi.managers.messages'));
		Assert::type('Igloonet\MailkitApi\Managers\UsersManager', $sl->getService('mailkitApi.managers.users'));
		Assert::type('Igloonet\MailkitApi\RPC\Client', $sl->getService('mailkitApi.rpc.client'));
	}
}

(new ExtensionTest)->run();