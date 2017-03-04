<?php

namespace App\Components;

use	Model,
	App\Forms,
	Nette\Application\UI\Control,
	Vodacek\Forms\Controls\DateInput;

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
		$this->template->setFile(__DIR__ . '/Navigation.latte');
		$date = new \DateTime($this->date);
		$this->template->date = $date->format('Y-m-d');
		$days = $date->format('j')-1;
		$date = $date->sub(new \DateInterval("P{$days}D"));
		$this->template->prev = $date->sub(new \DateInterval('P1M'))->format('Y-m-d');
		$this->template->next = $date->add(new \DateInterval('P2M'))->format('Y-m-d');
		$this->template->render();
	}

	protected function createComponentCalendarForm()
	{
		$date = isset($this->date) ? $this->date : date('Y-m-d');
		$form = new Forms\CalendarForm();
		$form->addDate('date', 'Datum:', DateInput::TYPE_DATE)
			->setDefaultValue(new \DateTime($date))
			->setAttribute('class', 'form-control')
			->setAttribute('onchange', 'submit()');
		$form->onSuccess[] = [$this, 'calendarFormSubmited'];
		return $form;
	}

	public function calendarFormSubmited(Forms\CalendarForm $form)
	{
		$values = $form->getValues();
		$this->redirect('this', array('date' => $values->date->format('Y-m-d')));
	}

	/**
	 * Loads state informations.
	 * @param  array
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		$this->getNavigation()->date = $this->date;
	}
}
