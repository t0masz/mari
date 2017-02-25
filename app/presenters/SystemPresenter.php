<?php

namespace App\Presenters;

use Nette,
	Model,
	App\Forms;


/**
 * System presenter.
 */
class SystemPresenter extends SecurePresenter
{
	/** @var Model\LogManager @inject */
	public $logManager;

	/** @var Model\SystemManager @inject */
	public $systemManager;

	/** @var Model\SetupManager @inject */
	public $config;

	public function renderLog()
	{
		$vp = $this['vp'];
		$paginator = $vp->getPaginator();
		$paginator->itemCount = $this->logManager->getCountAll();
		$this->template->page = $paginator->page;
		$this->template->logs = $this->logManager->findLast($paginator->itemsPerPage,$paginator->offset);
	}

	protected function createComponentAdminMailForm($name)
	{
		$form = new Forms\AdminMailForm($this, $name);
		$form->onSuccess[] = [$this, 'adminMailFormSubmitted'];
		return $form;
	}

	public function adminMailFormSubmitted(Forms\AdminMailForm $form)
	{
		$values = $form->getValues();
		$result = $this->systemManager->sendMailToAdmin($values,$this->getUser()->Identity);

		if ($result === TRUE) {
			$this->flashMessage('Zpráva byla v pořádku odeslána.', 'success');
		} else {
			$this->flashMessage('Došlo k chybě při odesílání zprávy.', 'danger');
		}
		$this->redirect('System:Mail');
	}

}
