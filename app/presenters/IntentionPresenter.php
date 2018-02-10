<?php

namespace App\Presenters;

use Nette,
	Nette\Forms\Controls\SubmitButton,
	Model,
	App\Forms;


/**
 * Intention presenter.
 */
class IntentionPresenter extends BasePresenter
{
	/** @var Model\IntentionManager @inject */
	public $intentionManager;


	public function handleIntention($date, $time, $intention = NULL, $id = NULL, $amount = NULL, $code = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','add')) {
			if(isset($code) && $code > 0) {
				$priest = $this->intentionManager->getNameByCode($code);
				if(isset($priest->name) && $priest->name != '') {
					if(isset($intention) && $intention != '') {
						if(isset($id) && $id > 0) {
							$this->flashMessage('Intence pro vybranou mši je již zadaná!', 'danger');
						} else {
							$values = [
								'date' => $date,
								'time' => $time,
								'intention' => $intention,
								'amount' => $amount,
							];
							$valuesLog = [
								'date' => $date,
								'time' => $time,
								'intention' => $intention,
								'amount' => $amount,
								'code_id' => $code,
								'ts' => new \DateTime()
							];
							$result = $this->intentionManager->save($values);
							$result = $this->intentionManager->insertLog($valuesLog);
						}
					} else {
						$this->flashMessage('Nemáš zadanou intenci!', 'danger');
					}
				} else {
					$this->flashMessage('Chybně vložený kontrolní kód!', 'danger');
				}
			} else {
				$this->flashMessage('Nebylo zadáno heslo!', 'danger');
			}
		} else {
			$this->flashMessage('Nemáš práva k editaci intencí!', 'danger');
		}
	}

	public function renderDefault($date = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','default')) {
			$navigation = $this['navigation'];
			$date = new \DateTime($navigation->date);
			$this->template->date = $date->format('Y-m-d');
			$days = $date->format('j')-1;
			$date = $date->sub(new \DateInterval("P{$days}D"));
			$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
			$this->template->items = $this->intentionManager->findByDate($date->format('Y-m-d'));
			if($this->isAjax()) {
				$this->redrawControl();
			}
		} else {
			$this->flashMessage('Nemáš práva pro nahlížení do intencí.', 'danger');
			$this->redirect('Homepage:');
		}
	}

	public function renderExport($date = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','export')) {
			$date = new \DateTime($date);
			$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
			$this->template->date = $date->format('Y-m-d');
			$this->template->items = $this->intentionManager->findByDateWeek($date->format('Y-m-d'));
			if($this->isAjax()) {
				$this->redrawControl();
			}
		} else {
			$this->flashMessage('Nemáš práva pro export intencí pro tisk.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderStatement($date = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','statement')) {
			$date = new \DateTime($date);
			$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
			$this->template->addFilter('currency', 'App\Helpers\Helpers::currency');
			$this->template->date = $date->format('Y-m-d');
			$items = $this->intentionManager->findByDateMonth($date->format('Y-m-d'));
			$this->template->items = $items;
			$this->template->summary = $this->intentionManager->getSummary($items,$date);
			if($this->isAjax()) {
				$this->redrawControl();
			}
		} else {
			$this->flashMessage('Nemáš práva pro nahlížení do výkazu intencí.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderEdit($id, $date)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','edit')) {
			$row = $this->intentionManager->getById($id);
			if (!$row) {
				$date = new \DateTime($date);
				$this->flashMessage('Intence nenalezena.', 'danger');
				$this->redirect('Intention:Statement',$date->format('Y-m-d'));
			}
			$this['intentionForm']->setDefaults($row);
		} else {
			$this->flashMessage('Nemáš práva pro editaci intencí.', 'danger');
			$this->redirect('Intention:');
		}
	}

	protected function createComponentIntentionForm($name)
	{
		$form = new Forms\IntentionForm($this, $name);
		$form->onSuccess[] = [$this, 'intentionFormSubmitted'];
		return $form;
	}

	public function intentionFormSubmitted(Forms\IntentionForm $form)
	{
		$values = $form->getValues();
		$date = new \DateTime($values['date']);
		$result = $this->intentionManager->save($values);
		$values['date'] = null;
		if ($result === 'inserted') {
			$this->flashMessage('Nová intence byla vložena', 'success');
		} elseif($result === 'updated') {
			$this->flashMessage('Intence byla upravena.', 'success');
		} elseif($result === FALSE) {
			$this->flashMessage('Došlo k chybě při ukládání.', 'danger');
		}
		$this->redirect('Intention:Statement',$date->format('Y-m-d'));
	}

	public function renderCode()
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','code')) {
			$items = $this->intentionManager->findAllCode();
			$this->template->items = $items;
			if($this->isAjax()) {
				$this->redrawControl();
			}
		} else {
			$this->flashMessage('Nemáš práva pro nahlížení a editaci kontrolních kódů.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderAddCode()
	{
		if(!$this->getUser()->isLoggedIn() && !$this->getUser()->isAllowed('Intention','code')) {
			$this->flashMessage('Nemáš práva pro editaci kontrolních kódů.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderEditCode()
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','code')) {
			$row = $this->intentionManager->getCodeById($id);
			if (!$row) {
				$date = new \DateTime($date);
				$this->flashMessage('Přístupový kód nenalezen.', 'danger');
				$this->redirect('Intention:Code');
			}
			$this['codeForm']->setDefaults($row);
		} else {
			$this->flashMessage('Nemáš práva pro editaci kontrolních kódů.', 'danger');
			$this->redirect('Intention:');
		}
	}

	protected function createComponentCodeForm($name)
	{
		$form = new Forms\CodeForm($this, $name);
		$form->onSuccess[] = [$this, 'codeFormSubmitted'];
		return $form;
	}

	public function codeFormSubmitted(Forms\CodeForm $form)
	{
		$values = $form->getValues();
		$result = $this->intentionManager->saveCode($values);

		if ($result == 'inserted') {
			$this->flashMessage('Nový kód byl vložen', 'success');
		} elseif($result == 'updated') {
			$this->flashMessage('Kód byl upraven.', 'success');
		} elseif($result == 'duplicate') {
			$this->flashMessage('Kód již existuje, zvol jiný kód.', 'danger');
		} else {
			$this->flashMessage('Došlo k chybě při ukládání.', 'danger');
		}
		$this->redirect('Intention:Code');
	}
	
	public function handleDisableCode($id) {
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','code')) {
			$row = $this->intentionManager->getCodeById($id);
			if ($row) {
				$result = $this->intentionManager->disableCode($id);
				if ($result) {
					$this->flashMessage('Přístupový kód zablokován.', 'success');
				} else {
					$this->flashMessage('Došlo k chybě při blokování bezpečnostního kódu.', 'danger');
				}
				$this->redirect('Intention:Code');
			}
		} else {
			$this->flashMessage('Nemáš práva pro editaci přístupových kódů.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderProtocol()
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','protocol')) {
			$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
			$this->template->addFilter('currency', 'App\Helpers\Helpers::currency');
			$vp = $this['vp'];
			$paginator = $vp->getPaginator();
			$paginator->itemCount = $this->intentionManager->getCountAllLog();
			$this->template->page = $paginator->page;
			$this->template->items = $this->intentionManager->findAllLog($paginator->itemsPerPage,$paginator->offset);
		} else {
			$this->flashMessage('Nemáš práva pro nahlížení do protokolu intencí.', 'danger');
			$this->redirect('Intention:');
		}
	}
}
