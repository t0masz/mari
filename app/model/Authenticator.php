<?php

namespace Model;

use Nette,
	Nette\Utils\Strings;


/**
* Users management.
*/
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'user',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD = 'password',
		COLUMN_ROLE = 'role',
		PASSWORD_MAX_LENGTH = 4096;


	/** @var UserRepository */
	private $userRepository;

	public function __construct(UserRepository $repository)
	{
		$this->userRepository = $repository;
	}


	/**
	 * Performs an authentication.
	 * @param array
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$row = $this->userRepository->findBy(array('username' => $username))->fetch();
		
		if (!$row) {
			throw new Nette\Security\AuthenticationException('Chybné uživatelské jméno.', self::IDENTITY_NOT_FOUND);
		}
		
		if ($row[self::COLUMN_PASSWORD] !== $this->calculateHash($password, $row[self::COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('Chybné heslo.', self::INVALID_CREDENTIAL);
		}
		
		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Computes salted password hash.
	 * @param string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		$password = substr($password, 0, self::PASSWORD_MAX_LENGTH);
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(22));
	}

}
