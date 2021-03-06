<?php

App::uses('ChameleonAppController', 'Chameleon.Controller');

class ChameleonController extends ChameleonAppController {

	public $_User;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->_User = ClassRegistry::init('Users.User');
		$this->Auth->allow('admin_restore_login');
	}

	public function admin_login_as($id = null) {
		$this->autoRender = false;
		$formerUser = $this->Session->read('Auth.User');
		if (!empty($id)) {
			$user = $this->_User->findById($id);
			if ($this->Auth->login($user['User'])) {
				$this->Session->write('Chameleon.User', $formerUser);
				$this->Session->setFlash(__d('Chameleon', 'Switched to %s', $user['User']['name']), 'flash', array('class' => 'success'));
			} else {
				$this->Session->setFlash(__d('Chameleon', 'failed to switch User'), 'flash', array('class' => 'error'));
			}
		}
		return $this->redirect('/');
	}

	public function admin_restore_login() {
		$formerUser = $this->Session->read('Chameleon.User');
		if (empty($formerUser)) {
			$this->Session->setFlash('Invalid request', 'flash', array('class' => 'error'));
			return $this->redirect('/');
		}
		$this->Session->delete('Chameleon.User');
		if ($this->Auth->login($formerUser)) {
			return $this->redirect(Configure::read('Croogo.dashboardUrl'));
		};
	}
}
