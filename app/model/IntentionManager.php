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

	public function __construct(IntentionRepository $repository)
	{
		$this->intentionRepository = $repository;
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
		return $this->intentionRepository->findBy(array('id' => (int)$id))->fetch();
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
		$services = array();
		for ($i = 0; $i < $month['days']; $i++) {
			$sun = $date->format('D') == 'Sun' ? TRUE : FALSE;
			$mon = $date->format('D') == 'Mon' ? TRUE : FALSE;
			$services[$date->format('Y-m-d-07-00-00')] = array('date' => $date->format('Y-m-d'), 'time' => '07:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$services[$date->format('Y-m-d-08-30-00')] = array('date' => $date->format('Y-m-d'), 'time' => '08:30:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$services[$date->format('Y-m-d-18-00-00')] = array('date' => $date->format('Y-m-d'), 'time' => '18:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->intentionRepository->findBySql('date BETWEEN ? AND ?',array($from->format('Y-m-d'),$to->format('Y-m-d')));
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
		$services = array();
		for ($i = 0; $i < 7; $i++) {
			$sun = $date->format('D') == 'Sun' ? TRUE : FALSE;
			$mon = $date->format('D') == 'Mon' ? TRUE : FALSE;
			$services[$date->format('Y-m-d-07-00-00')] = array('date' => $date->format('Y-m-d'), 'time' => '07:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$services[$date->format('Y-m-d-08-30-00')] = array('date' => $date->format('Y-m-d'), 'time' => '08:30:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$services[$date->format('Y-m-d-18-00-00')] = array('date' => $date->format('Y-m-d'), 'time' => '18:00:00', 'intention' => '', 'id' => '', 'sun' => $sun, 'mon' => $mon);
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->intentionRepository->findBySql('date BETWEEN ? AND ?',array($from->format('Y-m-d'),$to->format('Y-m-d')));
		foreach($result as $item) {
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['intention'] = $item->intention;
			$services[$item->date->format('Y-m-d').'-'.$item->time->format('%H-%I-%S')]['id'] = $item->id;
		}
		return $services;
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->intentionRepository->findBy(array('id' => (int)$id))->delete();
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
			$result = $this->intentionRepository->findBy(array('id' => (int)$id))->update($values);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->intentionRepository->insert($values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

}
