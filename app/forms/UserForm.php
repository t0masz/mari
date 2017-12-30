<?php

namespace App\Forms;

use Nette\Forms\Controls,
	Nextras\Forms\Rendering\Bs3FormRenderer,
	Nette\Application\UI\Form,
	Nette\ComponentModel\IContainer;

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

		// setup form rendering
		$renderer = $this->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		// make form and controls compatible with Twitter Bootstrap
		$this->getElementPrototype()->class('form-horizontal');
		$this->setRenderer(new Bs3FormRenderer());
	}
	
}

