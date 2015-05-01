<?php

namespace Model;

use Nette,
	Nette\Mail\Message,
	Nette\Utils\Strings;


/**
* Image Manager.
*/
class ImageManager extends Nette\Object
{

	/** 
	 * @var Model\ImageRepository
	 */
	public $imageRepository;

	public function __construct(ImageRepository $repository)
	{
		$this->imageRepository = $repository;
	}

	/**
	 * Get count of all images
	 * @return number of rows
	 */
	public function getCountAll()
	{
		return $this->imageRepository->countAll();
	}

	/**
	 * Find and get image by ID
	 * @return Nette\Database\Table\IRow
	 */
	public function getByID($id)
	{
		return $this->imageRepository->findBy(array('id' => (int)$id))->fetch();
	}

	/**
	 * Find all images
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->imageRepository->findAll();
	}

	/**
	 * Delete rows by ID
	 * @return number of deleted rows
	 */
	public function deleteById($id)
	{
		return $this->imageRepository->findBy(array('id' => (int)$id))->delete();
	}

	/**
	 * Save values
	 * @return string (inserted/updated) or FALSE on error
	 */
	public function save($values, $id = NULL)
	{
		if (isset($values->id) && ($values->id > 0)) {
			$result = $this->getByID($values->id)->update($values);
			\Tracy\Debugger::log($result);
			$return = $result > 0 ? 'updated' : FALSE;
		} else {
			$result = $this->imageRepository->insert((array)$values);
			$return = $result ? 'inserted' : FALSE;
		}
		return $return;
	}

}
