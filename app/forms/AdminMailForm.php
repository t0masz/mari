<?php

namespace App\Forms;

use Nette\Forms\Controls;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;

class AdminMailForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addTextArea('message', 'Text zprávy')
			->setRequired('Je nutné vyplnit zprávu.');
		$this->addSubmit('ok', 'Poslat zprávu');
        $this->setRenderer(new Bs3FormRenderer());
	}
	
}

