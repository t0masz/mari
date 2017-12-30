<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Intention Manager.
*/
class IntentionManager extends Nette\Object
{

	/** 
	 * @var Model\IntentionRepository
	 */
	public $intentionRepository;

	/** 
	 * @var Model\IntentionLogRepository
	 */
	public $intentionLogRepository;

	/** 
	 * @var Model\CodeRepository
	 */
	public $codeRepository;

	/** 
	 * @var Model\PriestRepository
	 */
	public $priestRepository;

	public function __construct(IntentionRepository $repository, IntentionLogRepository $logRepository, CodeRepository $codeRepository, PriestRepository $priestRepository)
	{
		$this->intentionRepository = $repository;
		$this->intentionLogRepository = $logRepository;
		$this->codeRepository = $codeRepository;
		$this->priestRepository = $priestRepository;
	}

	/**
	 * Get count of all items
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->intentionRepository->countAll();
	}

	/**
	 * Find and get item by ID
	 * @return Nette\Database\Table\IRow
	 */
	public function getByID($id)
	{
		return $this->intentionRepository->findBy(['id' => (int)$id])->fetch();
	}

	/**
	 * Find all items
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->intentionRepository->findAll();
	}

	/**
	 * Find by date
	 * @return Nette\Database\Table\Selection
	 */
	public function findByDate($date)
	{
		$from = new \DateTime($date);
		$date = new \DateTime($date);
		$month['days'] = $date->format('t');
		$month['month'] = $date->format('n');
		$services = [];
		for ($i = 0; $i < $month['days']; $i++) {
			$sun = $date->format('D') == 'Sun' ? TRUE : FALSE;
			$mon = $date->format('D') == 'Mon' ? TRUE : FALSE;
			$services[$date->format('Y-m-d-07-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '07:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-08-30-00')] = ['date' => $date->format('Y-m-d'), 'time' => '08:30:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-18-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '18:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->intentionRepository->findBySql('date BETWEEN ? AND ?',[$from->format('Y-m-d'),$to->format('Y-m-d')]);
		foreach($result as $item) {
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['intention'] = $item->intention;
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['id'] = $item->id;
		}
		return $services;
	}

	/**
	 * Find by date
	 * @return Nette\Database\Table\Selection
	 */
	public function findByDateWeek($date)
	{
		$from = new \DateTime($date);
		$date = new \DateTime($date);
		$services = [];
		for ($i = 0; $i < 7; $i++) {
			$sun = $date->format('D') == 'Sun' ? TRUE : FALSE;
			$mon = $date->format('D') == 'Mon' ? TRUE : FALSE;
			$services[$date->format('Y-m-d-07-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '07:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-08-30-00')] = ['date' => $date->format('Y-m-d'), 'time' => '08:30:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-18-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '18:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon];
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->intentionRepository->findBySql('date BETWEEN ? AND ?',[$from->format('Y-m-d'),$to->format('Y-m-d')]);
		foreach($result as $item) {
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['intention'] = $item->intention;
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['id'] = $item->id;
		}
		return $services;
	}

	/**
	 * Find by date
	 * @return array
	 */
	public function findByDateMonth($date)
	{
		$from = is_object($date) ? $date : new \DateTime($date);
		$from->modify('first day of this month');
		$date = clone $from;
		$services = [];
		for ($i = 0; $i < $from->format('t'); $i++) {
			$sun = $date->format('D') == 'Sun' ? TRUE : FALSE;
			$mon = $date->format('D') == 'Mon' ? TRUE : FALSE;
			$services[$date->format('Y-m-d-07-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '07:00:00', 'intention' => '', 'id' => '', 'name' => '', 'amount' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-08-30-00')] = ['date' => $date->format('Y-m-d'), 'time' => '08:30:00', 'intention' => '', 'id' => '', 'name' => '', 'amount' => '', 'sun' => $sun, 'mon' => $mon];
			$services[$date->format('Y-m-d-18-00-00')] = ['date' => $date->format('Y-m-d'), 'time' => '18:00:00', 'intention' => '', 'id' => '', 'name' => '', 'amount' => '', 'sun' => $sun, 'mon' => $mon];
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->intentionRepository->findBySql('date BETWEEN ? AND ?',[$from->format('Y-m-d'),$to->format('Y-m-d')]);
		foreach($result as $item) {
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['intention'] = $item->intention;
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['id'] = $item->id;
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['amount'] = $item->amount;
		}
		$result = $this->priestRepository->findBySql('date BETWEEN ? AND ?',[$from->format('Y-m-d'),$to->format('Y-m-d')]);
		foreach($result as $item) {
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['name'] = $item->names;
		}
		return $services;
	}

	/**
	 * Find by date
	 * @return array
	 */
	public function getSummary($services,$date)
	{
		$from = is_object($date) ? $date : new \DateTime($date);
		$from->modify('first day of this month');
		$to = clone $from;
		$to->modify('last day of this month');
		$summary = [];
		$result = $this->priestRepository->findBySql('date BETWEEN ? AND ?',[$from->format('Y-m-d'),$to->format('Y-m-d')])->group('names');
		foreach($result as $item) {
			$summary[Strings::webalize($item->names)]['name'] = $item->names;
			$summary[Strings::webalize($item->names)]['count'] = 0;
			$summary[Strings::webalize($item->names)]['amount'] = 0;
		}
		foreach($services as $item) {
			if($item['name']!='') {
				$summary[Strings::webalize($item['name'])]['count']++;
				$summary[Strings::webalize($item['name'])]['amount']+=$item['amount'];
			}
		}
		return $summary;
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->intentionRepository->findBy(['id' => (int)$id])->delete();
	}

	/**
	 * Save values
	 * @return string (inserted/updated) or FALSE on error
	 */
	public function save($values)
	{
		if (isset($values['id']) && ($values['id'] > 0)) {
			$id = $values['id'];
			unset($values['id']);
			$result = $this->intentionRepository->findBy(['id' => (int)$id])->update($values);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->intentionRepository->insert($values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

	/**
	 * Find and get item by Code
	 * @return Nette\Database\Table\IRow
	 */
	public function getNameByCode($code)
	{
		return $this->codeRepository->findBy(['id' => (int)$code])->fetch();
	}

	/**
	 * Save values to log
	 * @return bool
	 */
	public function insertLog($values)
	{
		$result = $this->intentionLogRepository->insert($values);
		$return = $result ? TRUE : FALSE;
		return $return;
	}

	/**
	 * Find all items
	 * @return Nette\Database\Table\Selection
	 */
	public function findAllLog($itemsPerPage,$offset)
	{
		return $this->intentionLogRepository->findAll()->limit($itemsPerPage,$offset)->order('ts DESC');
	}

	/**
	 * Get count of all users
	 * @return number of rows
	 */
	public function getCountAllLog()
	{
		return $this->intentionLogRepository->countAll();
	}

	/**
	 * Find all items
	 * @return Nette\Database\Table\Selection
	 */
	public function findAllCode()
	{
		return $this->codeRepository->findAll();
	}
}
