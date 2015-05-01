<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Log Manager.
*/
class LogManager extends Nette\Object
{

	/** @var Model\LogRepository */
	private $logRepository;

	public function __construct(LogRepository $repository)
	{
		$this->logRepository = $repository;
	}


	/**
	 * Save log record
	 * @return bool
	 */
	public function save($values)
	{
		$result = $this->logRepository->insert($values);
		$return = $result ? TRUE : FALSE;
		return $return;
	}

	/**
	 * Get count of all logs
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->logRepository->countAll();
	}

	/**
	 * Find last logs
	 * @return Nette\Database\Table\Selection
	 */
	public function findLast($itemsPerPage,$offset)
	{
		return $this->logRepository->findAll()->order('ts DESC')->limit($itemsPerPage,$offset);
	}


}
