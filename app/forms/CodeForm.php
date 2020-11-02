<?php

namespace App\Forms;

use Nette\Forms\Controls;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;

class CodeForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addText('name', 'Jméno');
		$this->addText('id', 'Přístupový kód')
			->setHtmlType('number');
		$this->addSubmit('save',  'uložit');
		$this->setRenderer(new Bs3FormRenderer());
	}
	
}

