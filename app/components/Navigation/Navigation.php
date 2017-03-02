<?php

namespace App\Components;

use	Model,
	Nette\Application\UI\Control;

class Navigation extends Control
{
	/** @var Navigaion */
	private $navigation;

	/** @persistent */
	public $date;

	/**
	 * @return Control\Navigation
	 */
	public function getNavigation()
	{
		if (!$this->navigation) {
			$this->navigation = new Navigation;
		}
		return $this->navigation;
	}

	public function render()
	{
#		\Tracy\Debugger::barDump($this->date);
		$this->template->setFile(__DIR__ . '/Navigation.latte');
		$date = new \DateTime($this->date);
		$this->template->date = $date->format('Y-m-d');
		$days = $date->format('j')-1;
		$date = $date->sub(new \DateInterval("P{$days}D"));
		$this->template->prev = $date->sub(new \DateInterval('P1M'))->format('Y-m-d');
		$this->template->next = $date->add(new \DateInterval('P2M'))->format('Y-m-d');
		$this->template->render();
	}


	/**
	 * Loads state informations.
	 * @param  array
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		\Tracy\Debugger::barDump($params);
		$this->getNavigation()->date = $this->date;
	}
}
