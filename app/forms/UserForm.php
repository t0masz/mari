<?php

namespace App\Forms;

use Nette\Forms\Controls;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;

class UserForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addText('name', 'Jméno');
		$this->addText('username', 'Uživatelské jméno');
		$this->addText('email', 'E-mail');
		$this->addSelect('role', 'Práva', ['admin' => 'Admin', 'editor' => 'Editor', 'priest' => 'Kněz', 'acolyte' => 'Ministrant', 'intention' => 'Intence'])
			->setRequired('Musíte vybrat uživatelská práva!');
		$this->addPassword('password', 'Nové heslo');
		$this->addSubmit('ok', 'Uložit');
        $this->setRenderer(new Bs3FormRenderer());
	}
	
}

