<?php

namespace App\Forms;

use Nette\Application\UI\Form,
	Nette\ComponentModel\IContainer,
	\Vodacek\Forms\Controls\DateInput;


class CalendarForm extends Form {

	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}

	protected function buildForm()
	{
	}

}

