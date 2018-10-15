<?php

namespace App\Forms;

use Nette\Application\UI\Form,
	Nextras\Forms\Rendering\Bs3FormRenderer;

class IntentionForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addHidden('id');
		$this->addHidden('date');
		$this->addHidden('time');
		$this->addText('intention', 'Intence');
		$this->addText('amount', 'Částka')
			->setHtmlType('number');
		$this->addText('code', 'Kód')
			->setAttribute('autocomplete', 'off');
		$this->addSubmit('save',  'uložit');
		$this->setRenderer(new Bs3FormRenderer());
	}
	
}

