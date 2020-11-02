<?php

namespace App\Presenters;

use Nette,
	Model,
	App\Forms;


/**
 * User presenter.
 */
class UserPresenter extends SecurePresenter
{
	/** @persistent */
	public $id;

	/** @var Model\UserManager @inject */
	public $userManager;


	public function renderDefault()
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('Homepage','edit')) {
			$vp = $this['vp'];
			$paginator = $vp->getPaginator();
			$paginator->itemCount = $this->userManager->getCountAll();
			$this->template->page = $paginator->page;
			$this->template->users = $this->userManager->findAll($paginator->itemsPerPage,$paginator->offset);
		} else {
			$this->flashMessage('Nemáš práva pro daný modul.', 'danger');
			$this->redirect('Homepage:');
		}
	}

	public function renderEdit($id, $page)
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('User','edit')) {
			$row = $this->userManager->getById($id);
			if (!$row) {
				$this->id = NULL;
				$this->flashMessage('Uživatel nenalezen.', 'danger');
				$this->redirect('User:');
			}
			$this['userForm']->setDefaults($row);
		} else {
			$this->flashMessage('Nemáš práva pro daný modul.', 'danger');
			$this->redirect('Homepage:');
		}
	}

	public function renderAdd()
	{
		if($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed('User','add')) {
			$this['userForm']->setDefaults(array(
			  'role' => 'acolyte',
			));
		} else {
			$this->flashMessage('Nemáš práva pro daný modul.', 'danger');
			$this->redirect('Homepage:');
		}
	}

	protected function createComponentUserForm($name)
	{
		$form = new Forms\UserForm($this, $name);
		$form->onSuccess[] = [$this, 'userFormSubmitted'];
		return $form;
	}

	public function userFormSubmitted(Forms\UserForm $form)
	{
		$values = $form->getValues();
		$result = $this->userManager->save($values,$this->id);

		$this->id = NULL;
		if ($result == 'inserted') {
			$this->flashMessage('Nový uživatel byl vytvořen', 'success');
		} elseif($result == 'updated') {
			$this->flashMessage('Uživatel byl změněn.', 'success');
		} else {
			$this->flashMessage('Došlo k chybě při ukládání.', 'danger');
		}
		$this->redirect('User:');
	}

	protected function createComponentPasswordForm($name)
	{
		$form = new Forms\PasswordForm($this, $name);
		$form->onSuccess[] = [$this, 'passwordFormSubmitted'];
		return $form;
	}

	public function passwordFormSubmitted(Forms\PasswordForm $form)
	{
		if($this->getUser()->isLoggedIn()) {
			$values = $form->getValues();
			$result = $this->userManager->savePassword($values,$this->user->identity->id);
	
			$this->id = NULL;
			if ($result == 'updated') {
				$this->flashMessage('Heslo bylo změněno.', 'success');
			} elseif($result == 'wrong') {
				$this->flashMessage('Špatně vložené staré heslo.', 'danger');
			} else {
				$this->flashMessage('Došlo k chybě při ukládání hesla.', 'danger');
			}
		} else {
			$this->flashMessage('Změnit heslo může pouze řádně přihlášený uživatel.', 'danger');
		}
		$this->redirect('this');
		
	}

	public function handleDelete($id)
	{
		if ($this->getUser()->isInRole('admin')) {
			$result = $this->userManager->deleteById($id);
			if ($result == 1) {
				$this->flashMessage('Uživatel byl smazán.', 'success');
			} else {
				$this->flashMessage('Při mazání uživatele došlo k chybě.', 'danger');
			}
			$this->id = NULL;
			$this->redirect('this');
		} else {
			$this->flashMessage('Nemáš práva pro mazání uživatelů!', 'danger');
			$this->redirect('this');
		}
	}

	public function handleSend($id)
	{
		if ($this->getUser()->isInRole('admin')) {
			$row = $this->userManager->getById($id);
			if (!$row) {
				$this->id = NULL;
				$this->page = NULL;
				$this->flashMessage('Uživatel nenalezen.', 'danger');
				$this->redirect('this');
			}
			$return = $this->userManager->sendPassword($id);
			$this->id = NULL;
			if ($return) {
				$this->flashMessage('Nové heslo bylo odesláno v pořádku.', 'success');
			} else {
				$this->flashMessage('Nové heslo se nepodařilo vygenerovat a odeslat.', 'danger');
			}
		} else {
			$this->flashMessage('Nemáš práva generovat a posílat nová hesla!', 'danger');
		}
		$this->redirect('this');
	}

}
