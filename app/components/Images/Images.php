<?php

namespace App\Components;

use	Model,
	Nette\Application\UI\Control;

class Images extends Control
{
	/** @var Model\ImageManager */
	private $imageManager;

	public function __construct(Model\ImageManager $model)
	{
//		parent::__construct(); # vždy je potřeba volat rodičovský konstruktor
		$this->imageManager = $model;
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/Images.latte');
		$this->template->images = $this->imageManager->findAll()->order('id');
		$this->template->render();
	}

}
