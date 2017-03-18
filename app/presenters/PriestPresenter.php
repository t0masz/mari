<?php

namespace App\Presenters;

use Nette,
	Nette\Forms\Controls\SubmitButton,
	Model,
	App\Forms;


/**
 * Priest presenter.
 */
class PriestPresenter extends BasePresenter
{
	/** @var Model\PriestManager @inject */
	public $priestManager;


	public function handlePriest($date, $time, $name = NULL, $id = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Priest','edit')) {
			if(isset($name) && $name != '') {
				if(isset($id) && $id > 0) {
					$currentValue = $this->priestManager->getByID($id);
					if($currentValue['names'] == $name) {
						$values = array(
							'id' => $id
						);
					} else {
						$values = array(
							'id' => $id,
							'names' => $name
						);
					}
				} else {
					$values = array(
						'date' => $date,
						'time' => $time,
						'names' => $name
					);
				}
				if (isset($values['names']) && $values['names'] != '')
					$result = $this->priestManager->save($values);
				elseif (isset($id) && $id > 0)
					$result = $this->priestManager->deleteById($id);
				else
					$this->flashMessage('Chyba při zpracování požadavku!', 'danger');
			} else {
				$this->flashMessage('Nemáš zadané jméno!', 'danger');
			}
		} else {
			$this->flashMessage('Nemáš práva k editaci služeb!', 'danger');
		}
	}

	public function renderDefault($date = NULL)
	{
		$navigation = $this['navigation'];
		$date = new \DateTime($navigation->date);
		$this->template->date = $date->format('Y-m-d');
		$days = $date->format('j')-1;
		$date = $date->sub(new \DateInterval("P{$days}D"));
		$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
		$this->template->items = $this->priestManager->findByDate($date->format('Y-m-d'));
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Priest','edit')) {
			$this->template->text = "editace pouze pro přihlášené s právy";
		} else {
			$this->template->text = "viditelné pro ostatní, neindexovat vyhledávači";
		}
		if($this->isAjax()) {
			$this->redrawControl();
		}
	}

	public function renderExport($date = NULL)
	{
		$date = new \DateTime($date);
		$this->template->addFilter('czechDate', 'App\Helpers\Helpers::czechDate');
		$this->template->date = $date->format('Y-m-d');
		$this->template->items = $this->priestManager->findByDateWeek($date->format('Y-m-d'));
		if($this->isAjax()) {
			$this->redrawControl();
		}
	}
}
