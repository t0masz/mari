<?php

namespace App\Forms;

use Nette\Forms\Controls,
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
		$this->addSelect('role', 'Práva', array('admin' => 'Admin', 'editor' => 'Editor', 'priest' => 'Kněz', 'acolyte' => 'Ministrant'))
			->addRule(Form::FILLED, 'Musíte vybrat uživatelská práva!');
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
		foreach ($this->getControls() as $control) {
			if ($control instanceof Controls\Button) {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
				$usedPrimary = TRUE;
			} elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
				$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
			}
		}
	}
	
}

