<?php

namespace App\Presenters;

use Nette\Http\Response;


/**
 * Url presenter.
 */
class UrlPresenter extends BasePresenter
{

	public function renderDefault($url)
	{
		$this->redirectUrl($url);
		exit;
	}

}
