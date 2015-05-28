<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Acolyte Manager.
*/
class AcolyteManager extends Nette\Object
{

	/** 
	 * @var Model\AcolyteRepository
	 */
	public $acolyteRepository;

	public function __construct(AcolyteRepository $repository)
	{
		$this->acolyteRepository = $repository;
	}

	/**
	 * Get count of all items
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->acolyteRepository->countAll();
	}

	/**
	 * Find and get item by ID
	 * @return Nette\Database\Table\IRow
	 */
	public function getByID($id)
	{
		return $this->acolyteRepository->findBy(array('id' => (int)$id))->fetch();
	}

	/**
	 * Find all items
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->acolyteRepository->findAll();
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
			$services[$date->format('Y-m-d')] = array('date' => $date->format('Y-m-d'), 'names1' => '', 'names2' => '', 'names3' => '', 'id1' => '', 'id2' => '', 'id3' => '');
			if($date->format('D') == 'Sun') $services[$date->format('Y-m-d')]['sun'] = TRUE;
			else $services[$date->format('Y-m-d')]['sun'] = FALSE;
			$date->add(new \DateInterval("P1D"));
		}
		$to = $date->sub(new \DateInterval("P1D"));
		$result = $this->acolyteRepository->findBySql('date BETWEEN ? AND ?',array($from->format('Y-m-d'),$to->format('Y-m-d')));
		foreach($result as $item) {
			if ($item->time->format('%H:%I:%S') == '07:00:00') {
				$services[$item->date->format('Y-m-d')]['names1'] = $item->names;
				$services[$item->date->format('Y-m-d')]['id1'] = $item->id;
			} elseif ($item->time->format('%H:%I:%S') == '08:30:00') {
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
		return $this->acolyteRepository->findBy(array('id' => (int)$id))->delete();
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
			$result = $this->acolyteRepository->findBy(array('id' => (int)$id))->update($values);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->acolyteRepository->insert($values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

}
