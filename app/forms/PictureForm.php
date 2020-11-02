<?php

namespace App\Forms;

use Nette\Forms\Controls;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;

class PictureForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addHidden('id');
		$this->addText('file', 'Jméno souboru');
		$this->addText('description', 'Pops');
		$this->addSubmit('ok', 'Uložit');
        $this->setRenderer(new Bs3FormRenderer());
	}
	
}

