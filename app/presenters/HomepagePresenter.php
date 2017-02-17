<?php

namespace App\Presenters;

use App\Forms,
	App\Components,
	Model,
	Nette,
	Nette\DateTime,
	Nette\Utils\Json,
	Nette\Forms\Controls\SubmitButton;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	public function renderDefault($id)
	{
		if ($id == '') {
			$page = $this->pageManager->getById(1);
		} else {
			$page = $this->pageManager->getBySlug($id);
		}
		$this->template->page = $page;
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Homepage','edit')) {
			$this['pageForm']->setDefaults(array(
			  'id' => $page->id,
				'content' => $page->content,
				'url' => $page->url,
				'name' => $page->name,
				'title' => $page->title
			));
		}
	}

	protected function createComponentPageForm($name)
	{
		$form = new Forms\PageForm($this, $name);
		$form->onSuccess[] = [$this, 'pageFormSucceeded'];
		return $form;
	}

	public function pageFormSucceeded(PageForm $form)
	{
		$values = $form->getValues();
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Homepage','edit')) {
		$return = $this->pageManager->save((array) $values, $values->id);
			if($return) {
				$flash_text = $return == 'inserted' ? 'přidán' : 'změněn';
				$this->presenter->flashMessage('Text byl v pořádku '.$flash_text.'.', 'success');
			} else {
				$this->presenter->flashMessage('Došlo k chybě při ukládání textu.', 'danger');
			}
		} else {
			$this->presenter->flashMessage('Nemáš práva pro ukládání textů stránek.', 'danger');
		}
		$this->redrawControl();
	}
	
}
