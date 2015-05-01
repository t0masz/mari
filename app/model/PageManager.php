<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Page Manager.
*/
class PageManager extends Nette\Object
{

	/** 
	 * @var Model\PageRepository
	 */
	public $pageRepository;

	public function __construct(PageRepository $repository)
	{
		$this->pageRepository = $repository;
	}

	/**
	 * Get page by ID
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id)
	{
		return $this->pageRepository->findBy(array('id' => (int)$id))->fetch();
	}

	/**
	 * Getpage by URL ID (Slug)
	 * @return Nette\Database\Table\Selection
	 */
	public function getBySlug($id)
	{
		return $this->pageRepository->findBy(array('url' => $id))->fetch();
	}

	/**
	 * Find all pages
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->pageRepository->findAll();
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->pageRepository->findBy(array('id' => (int)$id))->delete();
	}

	/**
	 * Save values
	 * @return string (inserted/updated) or FALSE on error
	 */
	public function save(array $values, $id = NULL)
	{
		if ($id) {
			$result = $this->pageRepository->findBy(array('id' => (int)$id))->update($values);
			\Tracy\Debugger::log($result);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->pageRepository->insert($values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

}
