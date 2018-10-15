<?php

namespace App\Presenters;

use Nette\Utils\DateTime,
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
								'code' => $code
							];
							$valuesLog = [
								'date' => $date,
								'time' => $time,
								'intention' => $intention,
								'amount' => $amount,
								'code_id' => $code,
								'type' => 'insert',
								'ts' => new DateTime()
							];
							$this->intentionManager->save($values);
							$this->intentionManager->insertLog($valuesLog);
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
			$date = new DateTime($navigation->date);
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
			$date = new DateTime($date);
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
			$date = new DateTime($date);
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
				$date = new DateTime($date);
				$this->flashMessage('Intence nenalezena.', 'danger');
				$this->redirect('Intention:Statement',$date->format('Y-m-d'));
			}
			$values['id'] = $row['id'];
			$values['date'] = $row['date']->format('Y-m-d');
			$values['time'] = $row['time']->format('%H:%I:%S');
			$values['intention'] = $row['intention'];
			$values['amount'] = $row['amount'];
			$this['intentionForm']->setDefaults($values);
		} else {
			$this->flashMessage('Nemáš práva pro editaci intencí.', 'danger');
			$this->redirect('Intention:');
		}
	}

	public function renderDelete($id, $date)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','edit')) {
			$row = $this->intentionManager->getById($id);
			if (!$row) {
				$date = new DateTime($date);
				$this->flashMessage('Intence nenalezena.', 'danger');
				$this->redirect('Intention:Statement',$date->format('Y-m-d'));
			}
			$values['id'] = $row['id'];
			$values['date'] = $row['date']->format('Y-m-d');
			$values['time'] = $row['time']->format('%H:%I:%S');
			$values['intention'] = $row['intention'];
			$values['amount'] = $row['amount'];
			$this['intentionDeleteForm']->setDefaults($values);
#			$this['intentionDeleteForm']['intention']->
			$this['intentionDeleteForm']['intention']->setAttribute('readonly','readonly');
			$this['intentionDeleteForm']['amount']->setAttribute('readonly','readonly');
			$this['intentionDeleteForm']['save']->caption = 'Smazat intenci';
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

	protected function createComponentIntentionDeleteForm($name)
	{
		$form = new Forms\IntentionForm($this, $name);
		$form->onSuccess[] = [$this, 'intentionDeleteFormSubmitted'];
		return $form;
	}

	public function intentionFormSubmitted(Forms\IntentionForm $form)
	{
		$values = $form->getValues();
		$date = new DateTime($values['date']);
		$result = $this->intentionManager->save($values);
		$values['date'] = null;
		if ($result === 'inserted') {
			$this->flashMessage('Nová intence byla vložena', 'success');
		} elseif($result === 'updated') {
			$this->flashMessage('Intence byla upravena.', 'success');
		} elseif($result === 'deleted') {
			$this->flashMessage('Intence byla smazána.', 'success');
		} elseif($result === 'nocode') {
			$this->flashMessage('Nebyl zadán kontrolní kód.', 'danger');
		} elseif($result === 'falsecode') {
			$this->flashMessage('Chybně zadaný kontrolní kód.', 'danger');
		} elseif($result === FALSE) {
			$this->flashMessage('Došlo k chybě při ukládání.', 'danger');
		}
		$this->redirect('Intention:Statement',$date->format('Y-m-d'));
	}

	public function intentionDeleteFormSubmitted(Forms\IntentionForm $form)
	{
		$values = $form->getValues();
		$date = new DateTime($values['date']);
		if(isset($values['code']) && $values['code'] > 0) {
			$priest = $this->intentionManager->getNameByCode($values['code']);
			if(isset($priest->name) && $priest->name != '') {
				$result = $this->intentionManager->deleteById($values['id']);
				$valuesLog = [
					'date' => $values['date'],
					'time' => $values['time'],
					'intention' => $values['intention'],
					'amount' => $values['amount'],
					'code_id' => $values['code'],
					'type' => 'delete',
					'ts' => new DateTime()
				];
				$this->intentionManager->insertLog($valuesLog);
				if ($result !== false) {
					$this->flashMessage('Intence byla smazána.', 'success');
				} else {
					$this->flashMessage('Došlo k chybě při mazání intence.', 'danger');
				}
				$this->redirect('Intention:Statement',$date->format('Y-m-d'));
			} else {
				$this->flashMessage('Chybně zadaný kontrolní kód.', 'danger');
			}
		} else {
			$this->flashMessage('Nezadaný kontrolní kód.', 'danger');
		}
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

	public function renderEditCode($id)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','code')) {
			$row = $this->intentionManager->getCodeById($id);
			if (!$row) {
				$date = new DateTime($date);
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

	public function renderProtocol($date)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','protocol')) {
			$navigation = $this['navigation'];
			$date = new DateTime($navigation->date);
			$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
			$this->template->addFilter('currency', 'App\Helpers\Helpers::currency');
			$this->template->addFilter('type', 'App\Helpers\Helpers::type');
			$vp = $this['vp'];
			$paginator = $vp->getPaginator();
	#		$date = $date ? $date : new DateTime();
			$by = ['date>=' => $date->format('Y-m-1'), 'date<=' => $date->format('Y-m-t')];
			$paginator->itemCount = $this->intentionManager->getCountLogBy($by);
			$this->template->page = $paginator->page;
			$this->template->items = $this->intentionManager->findLogBy($by,$paginator->itemsPerPage,$paginator->offset);
		} else {
			$this->flashMessage('Nemáš práva pro nahlížení do protokolu intencí.', 'danger');
			$this->redirect('Intention:');
		}
	}
}
