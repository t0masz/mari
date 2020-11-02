<?php

namespace App\Components;

use	Model,
	Nette\Application\UI\Control;

class Menu extends Control
{
	/** @var Model\PageManager */
	private $pageManager;

	public function __construct(Model\PageManager $model)
	{
//		parent::__construct(); # vždy je potřeba volat rodičovský konstruktor
		$this->pageManager = $model;
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/Menu.latte');
		$this->template->pages = $this->pageManager->findAll()->order('order');
		$this->template->render();
	}

}
