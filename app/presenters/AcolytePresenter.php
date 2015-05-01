<?php

namespace App\Presenters;

use Nette,
	Nette\Forms\Controls\SubmitButton,
	Nette\Utils\Strings,
	Model,
	App\Forms;


/**
 * Acolyte presenter.
 */
class AcolytePresenter extends BasePresenter
{
	/** @var Model\AcolyteManager @inject */
	public $acolyteManager;


	public function handleAcolyte($date, $time, $name = NULL, $id = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Acolyte','edit')) {
			if(isset($name) && $name != '') {
				if(isset($id) && $id > 0) {
					$currentValue = $this->acolyteManager->getByID($id);
					$names = Strings::replace($currentValue['names'], '~, ~i', ',');
					$names = explode(',', $names);
					$key = array_search($name, $names);
					if ($key !== FALSE) {
						unset($names[$key]);
						$values = array(
							'id' => $id,
							'names' => implode(', ', $names)
						);
					} else {
						$values = array(
							'id' => $id,
							'names' => $currentValue['names'] . ', ' . $name
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
					$result = $this->acolyteManager->save($values);
				elseif (isset($id) && $id > 0)
					$result = $this->acolyteManager->deleteById($id);
				else
					$this->flashMessage('Chyba při zpracování požadavku!', 'danger');
			} else {
				$this->flashMessage('Nemáš zadané jméno!', 'danger');
			}
		} else {
			$this->flashMessage('Nemáš práva k editaci služeb!', 'danger');
		}
		$date = new \DateTime($date);
		$days = $date->format('j')-1;
		$date = $date->sub(new \DateInterval("P{$days}D"));
		$this->template->items = $this->acolyteManager->findByDate($date->format('Y-m-d'));
		$this->template->prev = $date->sub(new \DateInterval('P1M'))->format('Y-m-d');
		$this->template->next = $date->add(new \DateInterval('P2M'))->format('Y-m-d');
		$this->redrawControl();
	}

	public function renderDefault($date = NULL)
	{
		$date = new \DateTime($date);
		$days = $date->format('j')-1;
		$date = $date->sub(new \DateInterval("P{$days}D"));
		$this->template->items = $this->acolyteManager->findByDate($date->format('Y-m-d'));
		$this->template->prev = $date->sub(new \DateInterval('P1M'))->format('Y-m-d');
		$this->template->next = $date->add(new \DateInterval('P2M'))->format('Y-m-d');
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Acolyte','edit')) {
			$this->template->text = "editace pouze pro přihlášené s právy";
		} else {
			$this->template->text = "viditelné pro ostatní, neindexovat vyhledávači";
		}
		if($this->isAjax()) {
			$this->redrawControl();
		}
	}

}
