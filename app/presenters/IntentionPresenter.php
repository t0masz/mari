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


	public function handleIntention($date, $time, $intention = NULL, $id = NULL)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','edit')) {
			if(isset($intention) && $intention != '') {
				if(isset($id) && $id > 0) {
					$currentValue = $this->intentionManager->getByID($id);
					if($currentValue['intention'] == $intention) {
						$values = array(
							'id' => $id
						);
					} else {
						$values = array(
							'id' => $id,
							'intention' => $intention
						);
					}
				} else {
					$values = array(
						'date' => $date,
						'time' => $time,
						'intention' => $intention
					);
				}
				if (isset($values['intention']) && $values['intention'] != '')
					$result = $this->intentionManager->save($values);
				elseif (isset($id) && $id > 0)
					$result = $this->intentionManager->deleteById($id);
				else
					$this->flashMessage('Chyba při zpracování požadavku!', 'danger');
			} else {
				$this->flashMessage('Nemáš zadanou intenci!', 'danger');
			}
		} else {
			$this->flashMessage('Nemáš práva k editaci intencí!', 'danger');
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
		$this->template->items = $this->intentionManager->findByDate($date->format('Y-m-d'));
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Intention','edit')) {
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
		$this->template->items = $this->intentionManager->findByDateWeek($date->format('Y-m-d'));
		if($this->isAjax()) {
			$this->redrawControl();
		}
	}
}
