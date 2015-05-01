<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Priest Manager.
*/
class PriestManager extends Nette\Object
{

	/** 
	 * @var Model\PriestRepository
	 */
	public $priestRepository;

	public function __construct(PriestRepository $repository)
	{
		$this->priestRepository = $repository;
	}

	/**
	 * Get count of all items
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->priestRepository->countAll();
	}

	/**
	 * Find and get item by ID
	 * @return Nette\Database\Table\IRow
	 */
	public function getByID($id)
	{
		return $this->priestRepository->findBy(array('id' => (int)$id))->fetch();
	}

	/**
	 * Find all items
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->priestRepository->findAll();
	}

	/**
	 * Find by date
	 * @return Nette\Database\Table\Selection
	 */
	public function findByDate($date)
	{
#		\Tracy\Debugger::barDump($date);
		$from = new \DateTime($date);
		$date = new \DateTime($date);
		$month['days'] = $date->format('t');
		$month['month'] = $date->format('n');
		$services = array();
		for ($i = 0; $i < $month['days']; $i++) {
			$services[$date->format('Y-m-d')] = array('date' => $date->format('Y-m-d'), 'names1' => '', 'names2' => '', 'names3' => '', 'id1' => '', 'id2' => '', 'id3' => '');
			if($date->format('D') == 'Sun') $services[$date->format('Y-m-d')]['sun'] = TRUE;
			else $services[$date->format('Y-m-d')]['sun'] = FALSE;
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->priestRepository->findBySql('date BETWEEN ? AND ?',array($from->format('Y-m-d'),$to->format('Y-m-d')));
#		\Tracy\Debugger::barDump($services);
		foreach($result as $item) {
#			\Tracy\Debugger::barDump($item->time->format('%H:%I:%S'));
			if ($item->time->format('%H:%I:%S') == '07:30:00') {
#				\Tracy\Debugger::barDump($item->time);
				$services[$item->date->format('Y-m-d')]['names1'] = $item->names;
				$services[$item->date->format('Y-m-d')]['id1'] = $item->id;
			} elseif ($item->time->format('%H:%I:%S') == '09:00:00') {
				$services[$item->date->format('Y-m-d')]['names2'] = $item->names;
				$services[$item->date->format('Y-m-d')]['id2'] = $item->id;
			} elseif ($item->time->format('%H:%I:%S') == '18:00:00') {
				$services[$item->date->format('Y-m-d')]['names3'] = $item->names;
				$services[$item->date->format('Y-m-d')]['id3'] = $item->id;
			}
		}
		return $services;
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->priestRepository->findBy(array('id' => (int)$id))->delete();
	}

	/**
	 * Save values
	 * @return string (inserted/updated) or FALSE on error
	 */
	public function save($values)
	{
		\Tracy\Debugger::log($values);
#		return TRUE;
		if (isset($values['id']) && ($values['id'] > 0)) {
			$id = $values['id'];
			unset($values['id']);
			$result = $this->priestRepository->findBy(array('id' => (int)$id))->update($values);
#			\Tracy\Debugger::log($result);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->priestRepository->insert($values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

}
