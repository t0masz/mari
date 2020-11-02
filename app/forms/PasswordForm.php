<?php

namespace App\Forms;

use Nette\Forms\Controls;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;

class PasswordForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addPassword('oldPassword', 'Staré heslo')
			->setRequired('Je nutné zadat staré heslo.');
		$this->addPassword('newPassword', 'Nové heslo')
			->setRequired('Je nutné zadat nové heslo.')
			->addRule(Form::MIN_LENGTH, 'Nové heslo musí mít alespoň %d znaků.', 6);
		$this->addPassword('confirmPassword', 'Potvrzení hesla')
			->setRequired('Nové heslo je nutné zadat ještě jednou pro potvrzení.')
			->addRule(Form::EQUAL, 'Zadná hesla se neshodují!', $this['newPassword']);
		$this->addSubmit('ok', 'Změnit heslo');
        $this->setRenderer(new Bs3FormRenderer());
	}
	
}

