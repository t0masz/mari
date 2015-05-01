<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class SecurePresenter extends BasePresenter
{
	protected function startup()
	{
		parent::startup();

		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->logoutReason === Nette\Http\UserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi odhlášen z důvodu delší neaktivity. Přihlas se prosím znovu.');
			}
			$this->redirect('Homepage:');
		}
		if (!$this->getUser()->isAllowed($this->name, $this->action)) {
			$this->flashMessage('Nemáš potřebná práva pro přístup na tuto stránku.', 'danger');
			$this->redirect('Homepage:');
		}
	}

}
