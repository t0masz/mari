<?php
namespace Model;
use Nette;

/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository
{

	/** @var Nette\Database\SelectionFactory  */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		if ($this->database) {
			throw new Nette\InvalidStateException('Database has already been set');
		}
		$this->database = $database;
	}

	/**
	 * Vrací objekt reprezentující databázovou tabulku.
	 * @return Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		// název tabulky odvodíme z názvu třídy
		preg_match('#(\w+)Repository$#', get_class($this), $m);
		return $this->database->table(lcfirst($m[1]));
	}

	/**
	 * Vrací všechny řádky z tabulky.
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->getTable();
	}

	/**
	 * Vrací řádky podle filtru, např. array('name' => 'John').
	 * @return Nette\Database\Table\Selection
	 */
	public function findBy(array $by)
	{
		return $this->getTable()->where($by);
	}

	/**
	 * Vrací řádky podle filtru, např. array('name' => 'John').
	 * @return Nette\Database\Table\Selection
	 */
	public function findBySql($str, array $by)
	{
		return $this->getTable()->where($str, $by);
	}

	/**
	 * Vrací počet řádků tabulky
	 * @return Nette\Database\Table\Selection
	 */
	public function count(array $by)
	{
		return $this->getTable()->where($by)->count("*");
	}

	/**
	 * Vrací počet všech řádků tabulky
	 * @return Nette\Database\Table\Selection
	 */
	public function countAll()
	{
		return $this->getTable()->count("*");
	}

	/**
	 * Vloží data do tabulky
	 * @return Nette\Database\Table\Selection
	 */
	public function insert(array $values)
	{
		return $this->getTable()->insert($values);
	}
}
