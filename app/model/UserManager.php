<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Mail\SendmailMailer,
	Nette\Mail\IMailer,
	Nette\Security\Passwords,
	Nette\Utils\Strings,
	Latte;


/**
* User Manager.
*/
class UserManager
{

	/** @var Model\UserRepository */
	private $userRepository;

	/** @var Model\Authenticator */
	private $authenticator;

	/** @var Nette\Mail\IMailer */
	private $mailer;

	/** @var Model\SetupManager */
	private $config;

	public function __construct(UserRepository $repository, Authenticator $authenticator, SetupManager $setup, IMailer $mailer)
	{
		$this->userRepository = $repository;
		$this->authenticator = $authenticator;
		$this->mailer = $mailer;
		$this->config = $setup;
	}


	/**
	 * Find and get user by username
	 * @return Nette\Database\Table\IRow
	 */
	public function getByUserName($username)
	{
		return $this->userRepository->findBy(array('username' => $username))->fetch();
	}

	/**
	 * Find and get user by ID
	 * @return Nette\Database\Table\IRow
	 */
	public function getByID($id)
	{
		return $this->userRepository->findBy(array('id' => (int)$id))->fetch();
	}

	/**
	 * Get count of all users
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->userRepository->countAll();
	}

	/**
	 * Find all users
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll($itemsPerPage,$offset)
	{
		return $this->userRepository->findAll()->limit($itemsPerPage,$offset);
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->userRepository->findBy(array('id' => (int)$id))->delete();
	}


	/**
	 * Save values
	 * @return string (inserted/updated) or FALSE on error
	 */
	public function save($values, $id = NULL)
	{
		if ($values->password == '')
			unset($values->password);
		else
			$values->password = Passwords::hash($values->password);
		if ($id) {
			$result = $this->userRepository->findBy(array('id' => (int)$id))->update((array)$values);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->userRepository->insert((array)$values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

	/**
	 * Save password
	 * @return string (wrong,updated)
	 */
	public function savePassword($values, $id)
	{
		$user = $this->userRepository->findBy(array('id' => (int)$id))->fetch();
		$password = Passwords::hash($values->newPassword);
		if (!Passwords::verify($values->oldPassword, $user->password)) {
			return 'wrong';
		} else {
			$result = $this->userRepository->findBy(array('id' => (int)$id))->update(array('password' => $password));
			$return = $result > 0 ? 'updated' : FALSE;
			return $return;
		}
	}

	/**
	 * Send new password
	 * @return bool
	 * @throws Model\SendMailException
	 */
	public function sendPassword($id)
	{
		$user = $this->userRepository->findBy(array('id' => (int)$id))->fetch();
		$possibleChars = 'abcdefghijkmnopqrstuvwxyz23456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
		$newPassword = '';
		$countChars = strlen($possibleChars);
		for ($i=0;$i<8;$i++) {
			$newPassword .= $possibleChars[mt_rand(0,$countChars - 1)];
		}
		$password = Passwords::hash($newPassword);
		$latte = new Latte\Engine;
		if (!$user->sent) {
			$template = __DIR__ . '/../templates/Email/firstPassword.latte';
		} else {
			$template = __DIR__ . '/../templates/Email/newPassword.latte';
		}
		$params = array(
			'password' => $newPassword,
			'username' => $user->username,
			'webUrl' => $this->config->web['url'],
			'webName' => $this->config->web['brand'],
			'webAdmin' => $this->config->mail['adminName']
		);
		$message = new Message;
		$message->setFrom($this->config->mail['adminMail'],$this->config->mail['adminName'])
			->addTo($user->email,$user->name)
			->setHtmlBody($latte->renderToString($template, $params));
		try {
			$this->mailer->send($message);
			$this->userRepository->findBy(array('id' => (int)$id))->update(array('password' => $password, 'sent' => new \DateTime()));
			return TRUE;
		} catch(Exception $e) {
			return FALSE;
		}
	}

}
