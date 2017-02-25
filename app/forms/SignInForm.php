<?php

namespace App\Forms;

use Nette\Application\UI\Form,
    Nette\ComponentModel\IContainer;

class SignInForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
    $this->addText('username', 'Uživatelské jméno');
    $this->addPassword('password', 'Heslo');
    $this->addCheckBox('remember', 'Neodhlašovat');
    $this->addSubmit('login', 'Přihlásit se');
	}
	
}

