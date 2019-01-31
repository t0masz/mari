<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
#	Nette\Mail\SendmailMailer,
	Nette\Mail\IMailer,
	Nette\Utils\Strings,
	Latte;

/**
* Setup management.
*/
class SystemManager
{

	/** @var Model\UserRepository */
	private $userRepository;

	/** @var Nette\Mail\IMailer */
	private $mailer;

	/** @var Model\SetupManager */
	private $config;

	public function __construct(UserRepository $repository, SetupManager $setup, IMailer $mailer)
	{
		$this->userRepository = $repository;
		$this->mailer = $mailer;
		$this->config = $setup;
	}

	/**
	 * Send message to admin
	 * @return bool
	 * @throws Model\SendMailException
	 */
	public function sendMailToAdmin($values, $userIdentity)
	{
		$latte = new Latte\Engine;
		$params = array(
			'note' => $values['message'],
			'webName' => $this->config->web['brand'],
			'userName' => $this->config->mail['adminName']
		);

		$message = new Message;
		$message->setFrom($userIdentity->email,$userIdentity->name)
			->addTo($this->config->mail['adminMail'],$this->config->mail['adminName'])
			->setHtmlBody($latte->renderToString('app/templates/Email/adminMessage.latte', $params));
		try {
			$this->mailer->send($message);
			return TRUE;
		} catch(Exception $e) {
			return FALSE;
		}
	}

}
