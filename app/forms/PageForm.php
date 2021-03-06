<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use Nextras\FormsRendering\Renderers\Bs3FormRenderer;
use Nette\Utils\Html;
use Nette\ComponentModel\IContainer;

class PageForm extends Form {
	
	public function __construct($parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->buildForm();
	}
	
	protected function buildForm()
	{
		$this->addHidden('id');
		$this->addTextArea('content', 'Obsah');
		$this->addText('url', 'URL adresa');
		$this->addText('name', 'Název stránky');
		$this->addText('title', 'Nápovědná bublina u odkazu');
		$this->addSubmit('save',  'uložit');
		$this->setRenderer(new Bs3FormRenderer());
	}
	
}

