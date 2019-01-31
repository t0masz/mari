<?php

namespace Model;

use Nette,
	Nette\Security\AuthenticationException,
	Nette\Security\Identity,
	Nette\Security\Passwords,
	Nette\Utils\Strings;


/**
* Users management.
*/
class Authenticator implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'user',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_EMAIL = 'email',
		COLUMN_ROLE = 'role';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Performs an authentication.
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		list($username, $password) = $credentials;
		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();
		if (!$row) {
			throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			]);
		}
		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

}
