<?php

namespace Model;

use Nette,
	Nette\Utils\Strings;


/**
* Setup management.
*/
class SetupManager extends Nette\Object
{

	public $web;
	public $paging;
	public $mail;
	public $gallery;

	public function __construct($config)
	{
		$this->web = $config['web'];
		$this->paging = $config['paging']['count'];
		$this->mail = $config['mail'];
		$this->gallery = $config['gallery'];
	}

}
